<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\FCM;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get_notifications(Request $request){

        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => 'Zone id is required!']);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $zone_id= $request->header('zoneId');
        try {
            $notifications = Notification::active()->where('tergat', 'customer')->where(function($q)use($zone_id){
                $q->whereNull('zone_id')->orWhere('zone_id', $zone_id);
            })->where('created_at', '>=', \Carbon\Carbon::today()->subDays(15))->get();
            $notifications->append('data');

            $user_notifications = UserNotification::where('user_id', $request->user()->id)->where('created_at', '>=', \Carbon\Carbon::today()->subDays(15))->get();
            $notifications =  $notifications->merge($user_notifications);
            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function push_notification(Request $request): JsonResponse
    {
        $data = $request->all();

        $fcmToken = match ($data['userType'])
        {
            'customer'    => User::find($data['userId'])->cm_firebase_token,
            'vendor'      => Vendor::find($data['userId'])->firebase_token,
            'deliveryman' => DeliveryMan::find($data['userId'])->fcm_token,
            default       => null,
        };

        if ($fcmToken) {
            if (FCM::sendMessage($data['notification'], $fcmToken)) {
                return response()->json(['message' => 'Notification sent successfully']);
            }
        }

        return response()->json(['errors' => ['message' => 'Notification sending failed']], 403);
    }

}
