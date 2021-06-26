<?php  namespace App\Libraries;

class Firebase {

    public static function send($to, $data) {
        if (empty($to)) {
            return 'device token empty';
        }
        $notification = array(
            'title' =>$data['title'],
            'body' => $data['message']
        );
        $fields = array(
            'to'        => $to,
            'notification' => $notification,
            'data' => $data
        );
        return self::sendPushNotification($fields);
    }

    public static function sendToTopic($topic, $data) {
        if (empty($to)) {
            return 'device token empty';
        }
        $notification = array(
            'title' =>$data['title'],
            'body' => $data['message']
        );
        $fields = array(
            'to' => '/topics/' . $topic,
            'notification' => $notification,
            'data' => $data
        );
        return self::sendPushNotification($fields);
    }

    public static function sendMultiple($registration_ids, $data) {
        if (count($registration_ids) == 0) {
            return 'devices token empty';
        }
        $notification = array(
            'title' =>$data['title'],
            'body' => $data['message']
        );
        $fields = array(
            'to' => $registration_ids,
            'notification' => $notification,
            'data' => $data
        );
        return self::sendPushNotification($fields);
    }

    private static function sendPushNotification($fields) {

        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . Setting::getFCMToken(),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($result === FALSE) {
            return $error;
        }

        return json_decode($result);
    }
}