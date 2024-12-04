<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use App\Models\AgeGroup;
use App\Models\Experience;
use Illuminate\Http\Request;
use App\Models\SessionType;
use App\Models\Country;
use App\Models\Category;
use App\Models\TimeZone;
use App\Models\ContactRequest;
use App\Models\Subscriber;

class GeneralApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        switch ($action) {
            case 'listing': {
                    return $this->listing($request);
                }
                break;

            case 'contact_requests': {
                    return $this->contact_requests($request);
                }
                break;

            case 'contacts': {
                    return $this->contacts($request);
                }
                break;

            case 'subscribers': {
                    return $this->subscribers($request);
                }
                break;

            default: {
                    return $this->sendError('Invalid Request');
                }
                break;
        }
    }

    private function listing($request) {
        $response = array();

        $all = 1;
        if ($request->input('listing_types')) {
            $listingTypes = $request->get('listing_types');

            if (in_array('countries', $listingTypes)) {
                $all = 0;
                $response['countries'] = $this->getCountries();
            }

            if (in_array('categories', $listingTypes)) {
                $all = 0;
                $response['categories'] = $this->getCategories();
            }

            if (in_array('sessionTypes', $listingTypes)) {
                $all = 0;
                $response['sessionTypes'] = $this->getSessionTypes();
            }

            if (in_array('time_zones', $listingTypes)) {
                $all = 0;
                $response['time_zones'] = $this->getTime_zones();
            }

            if (in_array('age_groups', $listingTypes)) {
                $all = 0;
                $response['age_groups'] = $this->getAge_groups();
            }

            if (in_array('experience', $listingTypes)) {
                $all = 0;
                $response['experience'] = $this->getExperiences();
            }
        }

        if ($all == 1) {
            $response['countries'] = $this->getCountries();
            $response['categories'] = $this->getCategories();
            $response['sessionTypes'] = $this->getSessionTypes();
            $response['time_zones'] = $this->getTime_zones();
            $response['age_groups'] = $this->getAge_groups();
            $response['experience'] = $this->getExperiences();
        }

        return $this->sendResponse($response, 'listing retrieved Successfully');
    }

    private function getCountries() {
        $response = NULL;
        $modelData = Country::select('name', 'id', 'tel')->where('status', 1)->get();
        if (count($modelData) > 0) {
            $response = array();
            foreach ($modelData as $data) {
                $response[] = [
                    'id'         => $data->id,
                    'name'       => $data->name,
                    'phone_code' => $data->tel
                ];
            }
        }

        return $response;
    }

    private function getcategories() {
        $response = NULL;
        $modelData = Category::select('name', 'id')->where('status', 1)->get();
        if (count($modelData) > 0) {
            $response = array();
            foreach ($modelData as $data) {
                $response[] = [
                    'id'   => $data->id,
                    'name' => $data->name
                ];
            }
        }

        return $response;
    }

    private function getSessionTypes() {
        $response = NULL;
        $modelData = SessionType::select('name', 'id')->where('status', 1)->get();
        if (count($modelData) > 0) {
            $response = array();
            foreach ($modelData as $data) {
                $response[] = [
                    'id'   => $data->id,
                    'name' => $data->name
                ];
            }
        }

        return $response;
    }

    private function getTime_zones() {
        $response = NULL;
        $modelData = TimeZone::select('display_name', 'id')->where('status', 1)->get();
        if (count($modelData) > 0) {
            $response = array();
            foreach ($modelData as $data) {
                $response[$data->id] = $data->display_name;
            }
        }

        return $response;
    }

    private function getAge_groups() {
        $ageGroups = array();
        $model = AgeGroup::get();
        foreach ($model as $key => $group) {
            $array = [];
            $array['value'] = $group->id;
            $array['text'] = $group->title;
            $ageGroups[] = $array;
        }

        return $ageGroups;
    }

    private function getExperiences() {
        $experiences = array();
        $model = Experience::get();
        foreach ($model as $key => $experience) {
            $array = [];
            $array['value'] = $experience->id;
            $array['text'] = $experience->title;
            $experiences[] = $array;
        }

        return $experiences;
    }

    private function contact_requests($request) {
        if (isset($request->name) && ltrim(rtrim($request->name)) != '' && isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->contact) && ltrim(rtrim($request->contact)) != '') {
            $name = $request->name;
            $email = $request->email;
            $contact = $request->contact;

            $ModelData = new ContactRequest();
            $ModelData->name = $name;
            $ModelData->email = $email;
            $ModelData->contact = $contact;
            $ModelData->status = 0;
            $ModelData->save();

            return $this->sendSuccess('Request submitted succesfully');
        }
        else {
            return $this->sendError('Missing Parameters');
        }
    }

    private function contacts($request) {
        if (isset($request->name) && ltrim(rtrim($request->name)) != '' && isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->website) && ltrim(rtrim($request->website)) != '' && isset($request->comment) && ltrim(rtrim($request->comment)) != '') {

            $response = $this->send_contact_request_email($request);

            if ($response['responseStatus'] === FALSE) {
                return $this->sendError($response['responseText']);
            }
            else {
                return $this->sendSuccess($response['responseText']);
            }
        }
        else {
            return $this->sendError('Missing Parameters');
        }
    }

    private function subscribers($request) {
        if (isset($request->name) && ltrim(rtrim($request->name)) != '' && isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->contact) && ltrim(rtrim($request->contact)) != '') {
            $name = $request->name;
            $email = $request->email;
            $contact = $request->contact;
            $date_time = date("Y-m-d H:i:s");

            $res = Subscriber::where('email', $email)->first();
            if (empty($res)) {

                $ModelData = new Subscriber();
                $ModelData->name = $name;
                $ModelData->email = $email;
                $ModelData->contact = $contact;
                $ModelData->date_time = $date_time;
                $ModelData->status = 0;
                $ModelData->save();

                return $this->sendSuccess('Succesfully Subscried');
            }
            else {
                return $this->sendSuccess('Subscription already exists');
            }
        }
        else {
            return $this->sendError('Missing Parameters');
        }
    }

}
