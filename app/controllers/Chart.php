<?php

use app\core\Controller;
use app\models\Analysis;

class Chart extends Controller
{
    protected $analysis = '';

    public function __construct()
    {

        parent::__construct();
        $this->analysis = new Analysis();

        //? check if authenticated
        if (!$this->isAuthenticated())
            return sendJsonResponse(
                'Unathorized',
                'Authentication is required to access this resource.',
                401
            );
    }

    public function getAllEndYears()
    {

        $this->analysis->query("SELECT end_year FROM data__5_ GROUP BY end_year");
        $result = $this->analysis->resultSet();

        $endYears = [];
        if (count($result) > 0) {

            //? loop through to remove empty endYears
            foreach ($result as $endYear) {
                if ($endYear->end_year == 'end_year') {
                    continue;
                }
                array_push($endYears, $endYear->end_year);
            }

            return sendJsonResponse(
                'success',
                'Data Retrived Successfully',
                200,
                ['endYears' => $endYears]
            );
        }

        return $endYears;
    }

    public function singleYaerAnalysis($year =  null)
    {

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (is_null($year)) {
                $year = date('Y');
                $endOfYearDatas  = $this->analysis->where('end_year', $year);

                sendJsonResponse(
                    'success',
                    'Data retrived successfully',
                    200,
                    ['endOfYearDatas' => $endOfYearDatas]
                );
            } else {
                $endOfYearDatas  = $this->analysis->where('end_year', $year);
                sendJsonResponse(
                    'success',
                    'Data retrived successfully',
                    200,
                    ['endOfYearDatas' => $endOfYearDatas]
                );
            }
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    public function yearsRanges(string $startYear = null, string $endYear = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (is_null($startYear) && is_null($endYear))
                return sendJsonResponse('error', 'Bad request provide start year and end year', 400);

            $yearsRangesAnalysis =  $this->analysis->row_exist(['start_year' => $startYear, 'end_year' => $endYear]);

            sendJsonResponse(
                'success',
                'Data Retrieved Successfully',
                200,
                ['yearsRangesAnalysis' => $yearsRangesAnalysis]
            );
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    public function countryCityAnalysis()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $country = $_POST['country'] ?? 'United States of America';
            $city = $_POST['city'] ?? null;

            if (is_null($city)) {
                $result = $this->analysis->where('country', $country);

                $dataToSend = [];
                if (count($result) > 0) {

                    //? loop through to remove empty cities
                    foreach ($result as $country) {
                        if (!empty($country->city))
                            array_push($dataToSend, $country);
                    }

                    return sendJsonResponse(
                        'success',
                        'Data Retrived Successfully',
                        200,
                        ['analysisByCountry' => $dataToSend]
                    );
                }

                return $dataToSend;
            } else {
                //? get country with associated city
                $analysisByCountry = $this->analysis->row_exist(['country' => $country, 'city' => $city]);

                sendJsonResponse(
                    'success',
                    'Data Retrieved Successfully',
                    200,
                    ['analysisByCountryCity' => $analysisByCountry]
                );
            }
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    public function getAllCountries()
    {
        $this->analysis->query("SELECT country FROM data__5_ GROUP BY country");
        $result = $this->analysis->resultSet();

        $countries = [];
        if (count($result) > 0) {

            //? loop through to remove empty countries
            foreach ($result as $country) {
                if (!empty($country->country))
                    array_push($countries, $country->country);
            }

            return sendJsonResponse(
                'success',
                'Data Retrived Successfully',
                200,
                ['countries' => $countries]
            );
        }

        return $countries;
    }

    public function getCitites()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $country = $_POST['country'];

            if (is_null($country))
                return sendJsonResponse('Unauthorized', 'Incorrect Data', 401);

            $this->analysis->query("SELECT city FROM data__5_ WHERE country = :country GROUP BY city");
            $this->analysis->execute(['country' => $country]);

            if ($this->analysis->resultSet() > 0) {
                $result = $this->analysis->resultSet();

                $cities = [];

                // //? loop through to remove empty countries
                foreach ($result as $city) {
                    if (!empty($city->city))
                        array_push($cities, $city->city);
                }

                return sendJsonResponse(
                    'success',
                    'Data Retrived Successfully',
                    200,
                    ['cities' => $cities]
                );
            }

            return [];
        }
    }


    public function getAllRegions()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->analysis->query("SELECT region FROM data__5_ GROUP BY region");
            $result = $this->analysis->resultSet();

            $regions = [];
            if (count($result) > 0) {

                //? loop through to remove empty regions
                foreach ($result as $region) {
                    if ($region->region == 'region' || empty($region->region)) {
                        continue;
                    }
                    array_push($regions, $region->region);
                }

                return sendJsonResponse(
                    'success',
                    'Data Retrived Successfully',
                    200,
                    ['regions' => $regions]
                );
            }

            return $regions;
        }
    }


    public function regionAnalysisByTopics()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $region = $_POST['region'] ?? null;
            $topic = $_POST['topic'] ?? null;

            if (is_null($region) || is_null($topic))
                return sendJsonResponse('error', 'Bad request provided for region', 400);

            //? get country with associated city
            $regionAnalysis = $this->analysis->row_exist(['region' => $region, 'topic' => $topic]);

            $dataToSend = [];
            if (count($regionAnalysis) > 0) {
                //? loop through to remove empty countries
                foreach ($regionAnalysis as $region) {
                    if (empty($region->country)) {
                        continue;
                    }
                    array_push($dataToSend, $region);
                }
            }


            sendJsonResponse(
                'success',
                'Data Retrieved Successfully',
                200,
                ['regionAnalysis' => $dataToSend]
            );
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    public function topicsAnalysis()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $topic = $_POST['topic'] ?? 'gas';

            if (is_null($topic))
                return sendJsonResponse('error', 'Bad request provided for topic', 400);

            //? get country with associated city
            $topicAnalysis = $this->analysis->where('topic', $topic);

            //? remove empty countries
            $dataToSend = [];
            foreach ($topicAnalysis as $topic) {
                if (!empty($topic->country)) {
                    array_push($dataToSend, $topic);
                }
            }

            sendJsonResponse(
                'success',
                'Data Retrieved Successfully',
                200,
                ['topicAnalysis' => $dataToSend]
            );
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    public function getAllTopics()
    {
        $this->analysis->query("SELECT topic FROM data__5_ GROUP BY topic");
        $result = $this->analysis->resultSet();

        $topics = [];
        if (count($result) > 0) {

            //? loop through to remove empty topic
            foreach ($result as $topic) {
                if (!empty($topic->topic))
                    array_push($topics, $topic->topic);
            }

            return sendJsonResponse(
                'success',
                'Data Retrived Successfully',
                200,
                ['topics' => $topics]
            );
        }

        return $topics;
    }


    public function getAllSectors()
    {
        $this->analysis->query("SELECT sector FROM data__5_ GROUP BY sector");
        $result = $this->analysis->resultSet();

        $sectors = [];
        if (count($result) > 0) {

            //? loop through to remove empty topic
            foreach ($result as $sector) {
                if (!empty($sector->sector))
                    array_push($sectors, $sector->sector);
            }

            return sendJsonResponse(
                'success',
                'Data Retrived Successfully',
                200,
                ['sectors' => $sectors]
            );
        }

        return $sectors;
    }


    public function sectorAnalysis()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sector = $_POST['sector'] ?? null;
            $endYear = $_POST['endYear'] ?? null;

            if (is_null($sector))
                return sendJsonResponse('error', 'Bad request provided for sector', 400);

            //? get country with associated city
            $sectorAnalysis = $this->analysis->row_exist(['sector' => $sector, 'end_year' => $endYear]);

            sendJsonResponse(
                'success',
                'Data Retrieved Successfully',
                200,
                ['sectorAnalysis' => $sectorAnalysis]
            );
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }



    public function getPestle()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->analysis->query("SELECT	pestle FROM data__5_ GROUP BY 	pestle");
            $result = $this->analysis->resultSet();

            if (count($result) > 0) {
                $pestles = [];

                //? loop through to remove empty regions
                foreach ($result as $pestle) {
                    if ($pestle->pestle == 'pestle' || empty($pestle->pestle)) {
                        continue;
                    }
                    array_push($pestles, $pestle->pestle);
                }

                return sendJsonResponse(
                    'success',
                    'Data Retrived Successfully',
                    200,
                    ['pestles' => $pestles]
                );
            }

            return [];
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    public function pestleAnalysis()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pestle = $_POST['pest'] ?? null;
            $endYear = $_POST['endYear'] ?? null;



            if (is_null($pestle) || is_null($endYear))
                return sendJsonResponse('error', 'Bad request provided for pest', 400);

            //? get country with associated city
            $pestleAnalysis = $this->analysis->row_exist(['pestle' => $pestle, 'end_year' => $endYear]);

            sendJsonResponse(
                'success',
                'Data Retrieved Successfully',
                200,
                ['pestleAnalysis' => $pestleAnalysis]
            );
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    public function swotAnalysis()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $swot = $_POST['swot'] ?? null;

            if (is_null($swot))
                return sendJsonResponse('error', 'Bad request provided for region', 400);

            //? get country with associated city
            $swotAnalysis = $this->analysis->where('swot', $swot);

            sendJsonResponse(
                'success',
                'Data Retrieved Successfully',
                200,
                ['swotAnalysis' => $swotAnalysis]
            );
        } else {

            //? if method not found
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }
}