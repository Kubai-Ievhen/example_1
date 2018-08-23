<?php
/**
 * Created by PhpStorm.
 * User: yevhen
 * Date: 12.06.18
 * Time: 10:28
 */

namespace App\Console;


use App\Mail\EventClosedMailing;
use App\Mail\NewEventsMailing;
use App\Mail\VolunteerReminderMailing;
use App\Models\Event\Event;
use App\Models\Event\EventStatus;
use App\Models\Event\EventVolunteerResponse;
use App\NewsLetterSubscription;
use App\User;
use Illuminate\Support\Facades\Mail;



class KernelFunctions
{
    /**
     * Mailing of Close Events
     */
    public static function closeEvent(){
        $close_status = EventStatus::where('name', 'closed')->first();

        $events = Event::where('finish_date','<=',date("Y-m-d"))->where('event_status_id','!=',$close_status->id)->with('user')->get();
        foreach ($events as $event) {
            Mail::to($event->user->email)
                ->send(new EventClosedMailing($event));
        }

        Event::where('finish_date','<=',date("Y-m-d"))->where('event_status_id','!=',$close_status->id)->update(['event_status_id'=>$close_status->id]);
    }

    /**
     * Mailing of Volunteer reminder
     */
    public static function volunteerReminder(){
        $close_status = EventStatus::where('name', 'closed')->first();

        $responses = EventVolunteerResponse::with('user')->with(['event_volunteer.event_demand.event'=>function($query) use ($close_status){
            $query->where('finish_date','<=',date("Y-m-d", mktime(23, 59, 59, date("m")  , date("d")+2, date("Y"))))
            ->where('event_status_id','!=',$close_status->id);
        }])->get();

        foreach ($responses as $response) {
            Mail::to($response->user->email)
                ->send(new VolunteerReminderMailing($response));
        }
    }

    public static function newEvents(){
        $new_events = Event::where('updated_at','>',date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))))
            ->where('updated_at','<=',date("Y-m-d H:i:s", mktime(23, 59, 59, date("m")  , date("d"), date("Y"))))
            ->where('is_approved',true)
            ->get();

        $users = User::where('newsletter',1)->get();
        $news_letters = NewsLetterSubscription::all();

        foreach ($users as $user) {
         self::sendMail($user->email, $new_events);
        }

        foreach ($news_letters as $news_letter) {
            self::sendMail($news_letter->email, $new_events);
        }

    }

    private static function sendMail($email, $events){
        Mail::to($email)
            ->send(new NewEventsMailing($events));
    }
}