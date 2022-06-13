<?php


namespace App\Services\v1;


use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CalendarService
{

    protected $calendarModel;

    public function __construct(Calendar $calendar)
    {
        $this->calendarModel = $calendar;
    }

    public function handleList(array $request)
    {
        $calendarEvents = $this->calendarModel
            ->where('date', '>', $request['date_start'])
            ->where('date', '<', $request['date_end'])
            ->get();

        return $calendarEvents;
    }


    public function countDateEnd($dateStart, $duration)
    {
        $date = new Carbon($dateStart);
        $dateStart = $date->parse($dateStart)->format('Y-m-d H:i');
        $dateEnd = $date->createFromFormat('Y-m-d H:i', $dateStart)->addMinutes($duration);

        return $dateEnd->toDateTimeString();

    }

    public function uncheckUserDate($newTask)
    {


        return $newTask;
    }

    public function setDate($updatedField, $calendarEvent)
    {
        $dateStart = $calendarEvent['date_start'];
        $dateEnd = $calendarEvent['date_end'];

        if (array_key_exists('date_start', $updatedField)) {
            $dateStart = $updatedField['date_start'];
        }
        if (array_key_exists('date_end', $updatedField)) {
            $dateEnd = $updatedField['date_end'];
        }

        if (array_key_exists('duration', $updatedField)) {
            $updatedField['date_end'] = $this->countDateEnd($dateStart, $updatedField['duration']);
            return $updatedField;
        }

        $current = Carbon::now();
        $date = Carbon::parse($dateEnd);
        $updatedField['duration'] = $date->diffInMinutes($current);
        $updatedField['date_end'] = $this->countDateEnd($dateStart, $calendarEvent['duration']);

        return $updatedField;
    }

    public function getError($id)
    {
        switch ($id) {
            case '1' :
                $error = [
                    'message' => 'You can\'t update events that are less than 3 hours away'
                ];
                break;
            case '2' :
                $error = [
                    'message' => 'Dates entered incorrectly'
                ];
                break;
            case '3' :
                $error = [
                    'message' => 'You can\'t delete events that are less than 3 hours away'
                ];
                break;
            case '4' :
                $error = [
                    'message' => 'Event on this time already exist'
                ];
                break;
        }

        return json_encode($error);
    }

    public function getDifferenceTime($time)
    {
        $diffTime = Carbon::parse($time)->diffInHours(Carbon::now());

        return $diffTime;
    }

    public function createEvent($requestData, $token = null)
    {
        if ($token) {
            $requestData = Calendar::create($requestData);
            return new CalendarResource($requestData);
        }
        $calendarEvents = $this->checkCurrentEvents($requestData);
        if (!$calendarEvents) {
            $requestData = Calendar::create($requestData);
            return new CalendarResource($requestData);
        }
        return $this->getError(4);
    }

    public function updateEvent($fields, $id)
    {
        $calendarEvent = Calendar::find($id);
        $diffTime = $this->getDifferenceTime($calendarEvent->date);

        if ($diffTime <= Calendar::LIMIT_HOURS) {
            return $this->getError(1);
        }

        Calendar::where('id', $id)->update($fields);

        return new CalendarResource($calendarEvent->refresh());
    }

    public function destroyEvent($id)
    {
        $calendarEvent = Calendar::findOrFail($id);
        $diffTime = $this->getDifferenceTime($calendarEvent->date);

        if ($diffTime <= Calendar::LIMIT_HOURS) {
            return $this->getError(3);
        }
        Calendar::destroy([$id]);

        return ['success' => 'You have successfully deleted the entry from the ID ' . $id];
    }

    public function checkCurrentEvents($requestData)
    {
        $eventsEnd = $this->countDateEnd($requestData['date'], $requestData['duration']);
        $calendarEvents = $this->calendarModel
            ->where('date', '>=', $requestData['date'])
            ->where('date', '<=', $eventsEnd)
            ->get();

        return $calendarEvents;
    }

}
