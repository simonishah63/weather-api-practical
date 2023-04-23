<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Repositories\CityRepository;
use App\Models\City;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;

/**
 * @OA\Info(
 *     description="API Documentation - Weather API",
 *     version="1.0.0",
 *     title="Weather API Documentation",
 *     @OA\Contact(
 *         email="simonishah63@gmail.com"
 *     ),
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * @OA\Tag(
 *     name="Weather Api",
 *     description="API Endpoints of weather Api"
 * )
 */
class CityController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * City Repository class.
     *
     * @var cityRepository
     */
    public $cityRepository;
    
    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }
    /**
     * @OA\POST(
     *     path="/api/v1/city",
     *     tags={"City"},
     *     summary="Create New City",
     *     description="Create New City",
     *     operationId="store",
     *     @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=true,
     *          description="City Name",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          example="Telang",
     *      ),
     *      @OA\Response(response=200, description="New City Created Successfully!'",       
     *          @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *      ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Content"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     * )
     */
    public function store(CityRequest $request)
    {
        try {
            $book = $this->cityRepository->create($request->all());
            return $this->responseSuccess($book, 'New City Created Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/v1/fetch-weather/{city}",
     *     tags={"City"},
     *     summary="Fetch weather for city",
     *     description="Provide list of city along with weather data",
     *     operationId="fetch-weather",
     *     @OA\Parameter(
     *          name="city",
     *          in="path",
     *          required=false,
     *          description="City Name",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          example="Telang",
     *      ),
     *      @OA\Response(response=200, description="City with weather info fetched successfully!'",       
     *          @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *      ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="City with weather info not found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     * )
     */
    public function fetchWeather($cityName = null)
    {
        try {
            $cityInfo = $this->cityRepository->fetchWeather($cityName);
            if(empty($cityInfo)) {
                return $this->responseError(null, 'City with weather info not found', Response::HTTP_NOT_FOUND);
            }
            return $this->responseSuccess($cityInfo, 'City with weather info fetched successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
