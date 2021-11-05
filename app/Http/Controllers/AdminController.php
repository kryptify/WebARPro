<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use App\Models\PlanBenefit;
use App\Models\Subscription;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{

    /**
     * Search users
    *
    * @return [json] users
    */
    public function fetchUsers(Request $request)
    {
       $q = $request->q;
       $options = $request->options; // sortBy = [], sortDesc = [], page = 0, itemsPerPage = 0 } = options
       $page = $options['page'];
       $perPage = $options['itemsPerPage'];
       $status = $request->status;
       $role = $request->role;
       $plan = $request->plan; // stripe_id
       $sortBy = $options['sortBy'] != null ? $options['sortBy'][0] : 'id';
       $sortDesc = $options['sortDesc'] != null && $options['sortDesc'][0] == true ? 'DESC' : 'ASC';

       return User::with('role', 'subscriptions.plan')
             ->select('users.*')
             ->join('roles', 'users.role_id', '=', 'roles.id')
             ->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
             ->leftJoin('plans', 'plans.stripe_id', '=', 'subscriptions.stripe_price')
             ->where(function ($query) use ($role) {
                if($role != null) {
                  $query->where('roles.id', $role);
                }
             })
             ->where(function ($query) use ($plan) {
                if($plan != null) {
                  $query->where('plans.stripe_id', $plan);
                }
             })
             ->where(function ($query) use ($status) {
                 if($status != null) {
                   $query->where('status', $status);
                 }
             })
             ->where(function ($query) use ($q) {
                 $query->where('username', "like", "%" . $q . "%");
                 $query->orWhere('email', "like", "%" . $q . "%");
             })
             ->orderBy($sortBy, $sortDesc)
             ->paginate(
                 $perPage, ['*'], 'users', $page
             );

       $users = User::all();
       return response()->json([
        'filteredData' => $users,
       ]);
    }

    /**
     * Add or edit the User ( username, email )
    *
    * @return [json] user object
    */
    public function addOrEditUser(Request $request)
    {
        $user_id = $request->id;
        $email = $request->email;

        $user_with_email = User::where('email', $email)->first();

        if($user_with_email != null && $user_with_email->id != $user_id) {
          return response()->json([
            'error'=>'The Email exists!',
            'email'=>'The Email exists!'
          ]);
        }

        if($user_id) {
          $user = User::find($user_id);
          if($request->username) $user->username = $request->username;
          if($request->email) $user->email = $request->email;
          if($request->role) $user->role_id = $request->role;
        } else {
          $user = new User([
              'username'  => $request->username,
              'email' => $request->email,
              'password' => bcrypt($request->password),
              'role_id' => $request->role,
          ]);
        }

        if($user->save()){

          if ($user_id) {
              $log_desc = 'Admin change user';
          } else {
              $log_desc = 'Admin create user '. $user->email;
          }

          // activity log
          activity()
          ->causedBy($request->user())
          ->performedOn($user)
          ->tap(function(Activity $activity) {
              $activity->type = 'admin';
          })
          ->log($log_desc);

          return response()->json([
            'success' => true,
            'userData' => User::with('role','subscriptions.plan')->find($user->id)
          ]);
        }
        else{
            return response()->json(['error'=>'User save failed!']);
        }
    }

    /**
     * Delete the user
    */
    public function deleteUser(Request $request)
    {
       $deleteUser = User::find($request->id);

       $user = $request->user();

       if ($user->role_id <= $deleteUser->role_id) {
           return response()->json([
              'error'=>'Unauthorized',
              'message'=>'You are not authorized to delete this user'
           ]);
       }

       if ($deleteUser->delete()) {

          // activity log
          activity()
          ->causedBy($user)
          ->performedOn($deleteUser)
          ->tap(function(Activity $activity) {
              $activity->type = 'admin';
          })
          ->log('Admin delete user');

          return response()->json([
           'success' => true
          ]);
       } else {
           return response()->json([
              'error'=>'User delete failed!',
              'message'=>'Unable to delete user!'
            ]);
       }
    }


    /**
     * Add or edit the plan benefit
    *
    * @return [json] plan benefit object
    */
    public function addOrEditPlanBenefit(Request $request)
    {
        $plan_benefit_id = $request->id;

        if($plan_benefit_id) {
          $plan_benefit = PlanBenefit::find($plan_benefit_id);
          if($request->plan) $plan_benefit->plan = $request->plan;
          if($request->desc) $plan_benefit->desc = $request->desc;
        } else {
          $plan_benefit = new PlanBenefit([
              'plan'  => $request->plan,
              'desc' => $request->desc,
          ]);
        }

        if($plan_benefit->save()){

          if ($plan_benefit_id) {
              $log_desc = 'Admin change plan benefit';
          } else {
              $log_desc = 'Admin create plan benefit for '. $plan_benefit->plan;
          }

          // activity log
          activity()
          ->causedBy($request->user())
          ->performedOn($plan_benefit)
          ->tap(function(Activity $activity) {
              $activity->type = 'admin';
          })
          ->log($log_desc);

          return response()->json([
            'success' => true,
            'planBenefitData' => PlanBenefit::all(),
          ]);
        }
        else{
            return response()->json(['error'=>'Plan benefit save failed!']);
        }
    }

    /**
     * Delete the plan benefit
    */
    public function deletePlanBenefit(Request $request)
    {
       $deleteItem = PlanBenefit::find($request->id);

       $user = $request->user();

       if ($deleteItem->delete()) {

          // activity log
          activity()
          ->causedBy($user)
          ->performedOn($deleteItem)
          ->tap(function(Activity $activity) {
              $activity->type = 'admin';
          })
          ->log('Admin delete benefit item');

          return response()->json([
           'success' => true,
          ]);
       } else {
           return response()->json([
              'error'=>'Plan benefit delete failed!',
              'message'=>'Unable to delete the plan benefit item',
            ]);
       }
    }
}
