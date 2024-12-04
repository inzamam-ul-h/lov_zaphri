<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        $pr_id = 0;
        $array = array();
        {   
            // pr_id = 1 'AB' 'Alberta'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Airdrie";
                    $sub_array[] = "Athabasca--Grande Prairie--Peace River Region";

                    $sub_array[] = "Calgary";

                    $sub_array[] = "Edmonton";

                    $sub_array[] = "Grande Prairie";

                    $sub_array[] = "Redwater";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 2 'BC' 'British Columbia'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Abbotsford";

                    $sub_array[] = "Cariboo Region";

                    $sub_array[] = "Kelowna";

                    $sub_array[] = "North Vancouver";

                    $sub_array[] = "Vancouver";
                    $sub_array[] = "Victoria";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 3 'MB' 'Manitoba'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Interlake Region";

                    $sub_array[] = "Winnipeg";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 4 'NB' 'New Brunswick'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Campbellton--Miramichi Region";

                    $sub_array[] = "Fredericton";

                    $sub_array[] = "Kedgwick";

                    $sub_array[] = "Memramcook";

                    $sub_array[] = "Quispamsis";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 5 'NL' 'Newfoundland and Labrador'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Avalon Peninsula Region";

                    $sub_array[] = "St. John's";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 6 'NT' 'Northwest Territories'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Yellowknife";
                    $sub_array[] = "Yellowknife Region";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 7 'NS' 'Nova Scotia'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Annapolis Valley Region";

                    $sub_array[] = "Halifax";

                $array[$pr_id] = $sub_array;
            }   

            // pr_id = 8 'NU' 'Nunavut'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Iqaluit";
                    $sub_array[] = "Iqaluit Region";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 9 'ON' 'Ontario'
            {
                $pr_id++;
                $sub_array = array();           


                    $sub_array[] = "Ajax"; 
                    $sub_array[] = "Alliston";
                    $sub_array[] = "Almonte";
                    $sub_array[] = "Ancaster";
                    $sub_array[] = "Arthur";
                    $sub_array[] = "Athens";
                    $sub_array[] = "Aurora"; 

                    $sub_array[] = "Baden";
                    $sub_array[] = "Baltimore";
                    $sub_array[] = "Barrie";
                    $sub_array[] = "Belle River";
                    $sub_array[] = "Belleville";
                    $sub_array[] = "Bolton"; 
                    $sub_array[] = "Bowmanville";
                    $sub_array[] = "Bradford";
                    $sub_array[] = "Brampton";
                    $sub_array[] = "Brantford";
                    $sub_array[] = "Breslau";
                    $sub_array[] = "Brooklin";
                    $sub_array[] = "Burlington";

                    $sub_array[] = "Caistor Centre";
                    $sub_array[] = "Caledon";
                    $sub_array[] = "Cambridge";
                    $sub_array[] = "Chatham";
                    $sub_array[] = "Colborne";
                    $sub_array[] = "Collingwood";
                    $sub_array[] = "Concord";
                    $sub_array[] = "Courtice";
                    $sub_array[] = "Cumberland";

                    $sub_array[] = "Delhi";
                    $sub_array[] = "Durham";

                    $sub_array[] = "Elora";
                    $sub_array[] = "Espanola";         
                    $sub_array[] = "Etobicoke"; 

                    $sub_array[] = "Fenelon Falls";
                    $sub_array[] = "Fergus";
                    $sub_array[] = "Fort Erie";
                    $sub_array[] = "Fort Frances";

                    $sub_array[] = "Georgetown";
                    $sub_array[] = "Geraldton";
                    $sub_array[] = "Goderich";
                    $sub_array[] = "Golden Lake";
                    $sub_array[] = "Grimsby";
                    $sub_array[] = "Guelph";

                    $sub_array[] = "Hamilton";            
                    $sub_array[] = "Hamilton--Niagara Peninsula Region";

                    $sub_array[] = "Innisfil";

                    $sub_array[] = "Kanata";            
                    $sub_array[] = "Kapuskasing";
                    $sub_array[] = "Kilworthy";
                    $sub_array[] = "King City";
                    $sub_array[] = "Kitchener";

                    $sub_array[] = "Lancaster";
                    $sub_array[] = "Lindsay";
                    $sub_array[] = "London";            

                    $sub_array[] = "Maple";
                    $sub_array[] = "Markdale";
                    $sub_array[] = "Markham";
                    $sub_array[] = "Midhurst";
                    $sub_array[] = "Milton";   
                    $sub_array[] = "Mississauga";           
                    $sub_array[] = "Mount Forest";

                    $sub_array[] = "Nepean";
                    $sub_array[] = "Newmarket";
                    $sub_array[] = "Niagara Falls";
                    $sub_array[] = "North Bay";                    
                    $sub_array[] = "North York"; 

                    $sub_array[] = "Oakville";
                    $sub_array[] = "Omemee";
                    $sub_array[] = "Orillia";
                    $sub_array[] = "Orton";
                    $sub_array[] = "Oshawa";
                    $sub_array[] = "Ottawa";
                    $sub_array[] = "Owen Sound";

                    $sub_array[] = "Paris";
                    $sub_array[] = "Pefferlaw";
                    $sub_array[] = "Peterborough";
                    $sub_array[] = "Pickering";
                    $sub_array[] = "Port Perry";

                    $sub_array[] = "Richmond";
                    $sub_array[] = "Richmond Hill";
                    $sub_array[] = "Rockwood";

                    $sub_array[] = "Sarnia";
                    $sub_array[] = "Scarborough";
                    $sub_array[] = "Schomberg";
                    $sub_array[] = "Shannonville";
                    $sub_array[] = "Simcoe";
                    $sub_array[] = "St. Catharines";
                    $sub_array[] = "St. Thomas";
                    $sub_array[] = "Stevensville";
                    $sub_array[] = "Stirling";
                    $sub_array[] = "Stittsville";
                    $sub_array[] = "Stoney Creek";
                    $sub_array[] = "Stouffville";            

                    $sub_array[] = "Thornhill";
                    $sub_array[] = "Thorold";
                    $sub_array[] = "Thornton";
                    $sub_array[] = "Thunder Bay";
                    $sub_array[] = "Toronto";
                    $sub_array[] = "Tottenham";

                    $sub_array[] = "Unionville";
                    $sub_array[] = "Uxbridge";

                    $sub_array[] = "Vaughan";

                    $sub_array[] = "Waterloo";
                    $sub_array[] = "Welland";
                    $sub_array[] = "Weston";
                    $sub_array[] = "Whitby";
                    $sub_array[] = "Windsor";
                    $sub_array[] = "Woodbridge";

                    $sub_array[] = "York";



                $array[$pr_id] = $sub_array;
            }

            // pr_id = 10 'PE' 'Prince Edward Island'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Charlottetown";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 11 'QC' 'Quebec'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Brossard";

                    $sub_array[] = "Cantley";
                    $sub_array[] = "Chesterville";

                    $sub_array[] = "Fabreville";

                    $sub_array[] = "Gatineau";

                    $sub_array[] = "Laval";

                    $sub_array[] = "Montreal";

                    $sub_array[] = "Pierrefonds";

                    $sub_array[] = "Quebec";

                    $sub_array[] = "Sherbrooke";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 12 'SK' 'Saskatchewan'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Northern Region";

                    $sub_array[] = "Regina";

                $array[$pr_id] = $sub_array;
            }

            // pr_id = 13 'YT' 'Yukon'
            {
                $pr_id++;
                $sub_array = array();
                    $sub_array[] = "Whitehorse";

                $array[$pr_id] = $sub_array;
            }
        }
		
		
		foreach($array as $pr_id => $sub_array)
		{		
            if(!empty($sub_array))
            {
                foreach($sub_array as $name)
                {
                    $name = trim($name);
                    if($name != '')
                    {
                        $model = new City();
                            $model->state_id = $pr_id;
                            $model->name = $name;
                            // $model->name_fr = $name;
                            //$model->code = $code;
                            $model->created_by = "1";
                        $model->save();
                    }
                }
            }
		}
    }
}