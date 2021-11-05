<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use App\Models\PlanBenefit;
use App\Models\Subscription;
use Spatie\Activitylog\Models\Activity;
use Validator;
use Carbon\Carbon;

class UserController extends Controller
{

    /**
     * Add or edit my info ( username, email )
    *
    * @return [json] user object
    */
    public function addOrEditMe(Request $request)
    {
        $user = $request->user();

        $user_id = $user->id;
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

          // activity log
          activity()
          ->causedBy($user)
          ->performedOn($user)
          ->tap(function(Activity $activity) {
              $activity->type = 'user';
          })
          ->log('User change login info');

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
    *  Edit the my password ( password )
    *
    * @return [json] user object
    */
    public function editUserPassword(Request $request)
    {
        $request->validate([
            'password'=>'required|string',
            'c_password' => 'required|same:password'
        ]);

        $user = $request->user();
        $user_id = $user->id;
        $user = User::find($user_id);
        $user->password = bcrypt($request->password);

        if($user->save()){

          // activity log
          activity()
          ->causedBy($user)
          ->performedOn($user)
          ->tap(function(Activity $activity) {
              $activity->type = 'user';
          })
          ->log('User change password');

          return response()->json([
            'success' => true,
            'userData' => User::with('role','subscriptions.plan')->find($user_id)
          ]);
        }
        else{
            return response()->json(['error'=>'User save failed!']);
        }
    }

    /**
     * Get the user with id
    *
    * @return [json] user
    */
    public function fetchUser(Request $request)
    {
         $user = User::with('role','subscriptions.plan')->find($request->id);
         if($user) {
            return response()->json([
              'success' => true,
              'userData' => $user
            ]);
         } else {
            return response()->json([
             'error' => 'Invalid user id'
            ]);
         }
    }

    /**
     * Get the all users
    *
    * @return [json] users
    */
    public function users(Request $request)
    {
       $users = User::all();
       return response()->json([
        'users' => $users
       ]);
    }

    /**
     * Get the all roles
    *
    * @return [json] roles
    */
    public function roles(Request $request)
    {
       $roles = Role::all();
       return response()->json([
        'roles' => $roles
       ]);
    }

    /**
       * Get the all plans
      *
      * @return [json] plans
      */
      public function plans(Request $request)
      {
         $plans = Plan::all();
         return response()->json([
          'plans' => $plans
         ]);
      }

      /**
       * Get the all plan benefits
       *
       * @return [json] plan benefits
      */
      public function planBenefits(Request $request)
      {
         $planBenefits = PlanBenefit::all();
         return response()->json([
          'planBenefits' => $planBenefits
         ]);
      }

    /**
     * Get my activities
    *
    * @return [json] activities
    */
    public function activities(Request $request)
    {
       $user = $request->user();
       $limit = $request->limit;
       $offset = $request->offset;

       $activities = Activity::where('causer_id', $user->id)
                   ->orWhere('subject_id', $user->id)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)->offset($offset)->get();

       return response()->json([
        'success' => true,
        'activities' => $activities
       ]);
    }

    /**
     * Creates an intent for payment so we can capture the payment
     * method for the user.
     *
     * @param Request $request The request data from the user.
     */
    public function getSetupIntent( Request $request ){
        return $request->user()->createSetupIntent();
    }

    /**
     * Adds a payment method to the current user.
     *
     * @param Request $request The request data from the user.
    */
    public function postPaymentMethods( Request $request ){
        $user = $request->user();
        $paymentMethodID = $request->get('payment_method');

        if( $user->stripe_id == null ){
            $user->createAsStripeCustomer();
        }

        $user->addPaymentMethod( $paymentMethodID );
        $user->updateDefaultPaymentMethod( $paymentMethodID );

        // activity log
        activity()
        ->causedBy($user)
        ->performedOn($user)
        ->tap(function(Activity $activity) {
            $activity->type = 'payment';
        })
        ->log('User add payment method');

        return response()->json( null, 204 );
    }

    /**
     * Returns the payment methods the user has saved
     *
     * @param Request $request The request data from the user.
     */
    public function getPaymentMethods( Request $request ){
        $user = $request->user();

        $methods = array();

        if( $user->hasPaymentMethod() ){
            foreach( $user->paymentMethods() as $method ){
                array_push( $methods, [
                    'id' => $method->id,
                    'brand' => $method->card->brand,
                    'last_four' => $method->card->last4,
                    'exp_month' => $method->card->exp_month,
                    'exp_year' => $method->card->exp_year,
                ] );
            }
        }

        return response()->json( $methods );
    }

    /**
     * Removes a payment method for the current user.
     *
     * @param Request $request The request data from the user.
     */
    public function removePaymentMethod( Request $request ){
        $user = $request->user();
        $paymentMethodID = $request->get('id');

        $paymentMethods = $user->paymentMethods();

        foreach( $paymentMethods as $method ){
            if( $method->id == $paymentMethodID ){
                $method->delete();
                break;
            }
        }

        // activity log
        activity()
        ->causedBy($user)
        ->performedOn($user)
        ->tap(function(Activity $activity) {
            $activity->type = 'payment';
        })
        ->log('User remove payment method');

        return response()->json( null, 204 );
    }

    /**
     * Updates a subscription for the user
     *
     * @param Request $request The request containing subscription update info.
     */
    public function updateSubscription( Request $request ) {
        $user = $request->user();
        $planID = $request->get('plan');
        $paymentID = $request->get('payment');

        if( $user->subscribed('default') == null ) {
            $user->newSubscription( 'default', $planID )
                    ->create( $paymentID );
        } else {
            $user->subscription('default')->swap( $planID );
        }

        // activity log
        activity()
        ->causedBy($user)
        ->performedOn($user)
        ->tap(function(Activity $activity) {
            $activity->type = 'payment';
        })
        ->log('User update subscription');
        $user_id = $user->id;
        return response()->json([
            'subscription_updated' => true,
            'userData' => User::with('role','subscriptions.plan')->find($user_id)
        ]);
    }


}
