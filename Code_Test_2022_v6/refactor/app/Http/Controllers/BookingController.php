<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            $jobs = $this->bookingRepository->getUsersJobs($request->get('user_id'));
        } else {
            $userType = $request->__authenticatedUser->user_type;
            $isAdmin = $userType == env('ADMIN_ROLE_ID') || $userType == env('SUPERADMIN_ROLE_ID');
            $jobs = $isAdmin ? $this->bookingRepository->getAll($request) : [];
        }
        
        return response($jobs);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->bookingRepository->with('translatorJobRel.user')->find($id);

        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $user = $request->__authenticatedUser;
        $data = $request->all();
        $response = $this->bookingRepository->store($user, $data);

        return response($response);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $user = $request->__authenticatedUser;
        $data = $request->except(['_token', 'submit']);
        $response = $this->bookingRepository->updateJob($id, $data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $data = $request->all();
        $response = $this->bookingRepository->storeJobEmail($data);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        $user_id = $request->get('user_id');
        $response = $user_id ? $this->bookingRepository->getUsersJobsHistory($user_id, $request) : [];

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->acceptJob($data, $user);

        return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->acceptJobWithId($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->cancelJobAjax($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();

        $response = $this->bookingRepository->endJob($data);

        return response($response);

    }

    public function customerNotCall(Request $request)
    {
        $data = $request->all();

        $response = $this->bookingRepository->customerNotCall($data);

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->bookingRepository->getPotentialJobs($user);

        return response($response);
    }

    public function distanceFeed(Request $request)
    {
        $job = Job::findOrFail($data['jobid']);
        $distanceUpdated = false;
        $jobUpdated = false;
    
        if (isset($data['distance']) || isset($data['time'])) {
            $distanceUpdated = Distance::where('job_id', $job->id)->update([
                'distance' => $data['distance'] ?? null,
                'time' => $data['time'] ?? null,
            ]);
        }
        if ($data['flagged'] == 'true' && empty($data['admincomment'])) {
            return response('Please, add comment');
        }
        if (isset($data['session_time']) || isset($data['flagged']) || isset($data['admincomment'])) {
            $job->fill([
                'admin_comments' => $data['admincomment'] ?? null,
                'flagged' => $data['flagged'] ? 'yes' : 'no',
                'session_time' => $data['session_time'] ?? null,
                'manually_handled' => $data['manually_handled'] == 'true' ? 'yes' : 'no',
                'by_admin' => $data['by_admin'] == 'true' ? 'yes' : 'no',
            ]);
            $jobUpdated = $job->save();
        }

        return $distanceUpdated || $jobUpdated ? response('Record updated!') : response('No changes made.');
    }

    public function reopen(Request $request)
    {
        $data = $request->all();
        $response = $this->bookingRepository->reopen($data);

        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->bookingRepository->find($data['jobid']);
       
        $jobData = $this->bookingRepository->jobToData($job);
        $this->bookingRepository->sendNotificationTranslator($job, $jobData, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->bookingRepository->find($data['jobid']);
        $jobData = $this->bookingRepository->jobToData($job);

        try {
            $this->bookingRepository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
