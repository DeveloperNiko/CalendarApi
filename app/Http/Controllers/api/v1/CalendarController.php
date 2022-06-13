<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\requests\v1\Calendar\CreateRequest;
use App\Http\requests\v1\Calendar\DestroyRequest;
use App\Http\requests\v1\Calendar\ShowRequest;
use App\Http\requests\v1\Calendar\UpdateRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use App\Services\v1\CalendarService;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{

    /**
     * @var CalendarService
     */
    protected $calendarService;


    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Display the specified resource.
     *
     * @param ShowRequest $showRequest
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $showRequest)
    {
        return $this->calendarService->handleShow($showRequest->all());
    }

    /**
     * Show the form for creating a new resource.
     * @param CreateRequest $createRequest
     * @return CalendarResource
     */
    public function create(CreateRequest $createRequest)
    {
        $newTask = $createRequest->all();
        $newTask['date_end']= $this->calendarService->countDateEnd($newTask['date_start'], $newTask['duration'] );
        if(!Auth::check()){
            $newTask = $this->calendarService->uncheckUserDate($newTask);
        }
        $newTask = Calendar::create($newTask);

        return new CalendarResource($newTask);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $updateRequest
     * @param int $id
     * @return CalendarResource|String
     */
    public function update(UpdateRequest $updateRequest, $id)
    {
        $calendarEvent = Calendar::find($id);
        $diffTime = $this->calendarService->getDifferenceTime($calendarEvent->date_start);

        if ($diffTime <= Calendar::LIMIT_HOURS) {
            return $this->calendarService->getError(1);
        }

        $updatedField = $this->calendarService->setDate($updateRequest->all(), $calendarEvent);
        if ($updatedField['duration'] <= 0) {
            return $this->calendarService->getError(2);
        }

        Calendar::where('id', $id)->update($updatedField);

        return new CalendarResource($calendarEvent->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $destroyRequest
     * @param int $id
     * @return String|array
     */
    public function destroy(DestroyRequest $destroyRequest,$id)
    {
        $calendarEvent = Calendar::findOrFail($id);
        $diffTime = $this->calendarService->getDifferenceTime($calendarEvent->date_start);

        if ($diffTime <= Calendar::LIMIT_HOURS) {
            return $this->calendarService->getError(3);
        }
        Calendar::destroy([$id]);

        return ['success' => 'You have successfully deleted the entry from the ID ' . $id];

    }

}
