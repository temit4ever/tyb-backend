<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use Illuminate\Http\Request;
use App\Http\Resources\Sprint as SprintResource;

/**
 * Class SprintController
 * @package App\Http\Controllers
 */
class SprintController extends Controller
{
    /**
     * @OA\Get(
     **  path="/v1/sprints",
     *   summary="List all sprints",
     *   operationId="getAllSprints",
     *   tags={"Sprints"},
     *
     *   @OA\Response(
     *     response=200,
     *     description="Successfully retrieved",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Sprint")
     *     ),
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad request"
     *   )
     *)
     **/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SprintResource::collection(Sprint::all());
    }

    /**
     * @OA\Post(
     **  path="/v1/sprints",
     *   summary="Create a sprint",
     *   operationId="createSprint",
     *   tags={"Sprints"},
     *
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(
     *             property="title",
     *             description="The title of the sprint",
     *             type="string"
     *         ),
     *         @OA\Property(
     *           property="start_date",
     *           description="The sprint start date",
     *           type="string",
     *           format="date"
     *         ),
     *         @OA\Property(
     *           property="end_date",
     *           description="The sprint end date",
     *           type="string",
     *           format="date"
     *         ),
     *         required={"title","start_date","end_date"}
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Successfully created",
     *     @OA\JsonContent(ref="#/components/schemas/Sprint"),
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not found"
     *   )
     *)
     **/
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        return new SprintResource(Sprint::create($request->all()));
    }

    /**
     * @OA\Get(
     **  path="/v1/sprints/{id}",
     *   summary="Get a sprint",
     *   operationId="getOneSprint",
     *   tags={"Sprints"},
     *
     *   @OA\Parameter(
     *     description="ID of the sprint to return",
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(
     *       type="integer",
     *       format="int64"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successfully retrieved",
     *     @OA\JsonContent(ref="#/components/schemas/Sprint"),
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not found"
     *   )
     *)
     **/
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function show(Sprint $sprint)
    {
        return new SprintResource($sprint);
    }

    /**
     * @OA\Put(
     **  path="/v1/sprints/{$id}",
     *   summary="Update a sprint",
     *   operationId="updateSprint",
     *   tags={"Sprints"},
     *
     *   @OA\Parameter(
     *     description="ID of the sprint to update",
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(
     *       type="integer",
     *       format="int64"
     *     )
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(
     *             property="title",
     *             description="The title of the sprint",
     *             type="string"
     *         ),
     *         @OA\Property(
     *           property="start_date",
     *           description="The sprint start date",
     *           type="string",
     *           format="date"
     *         ),
     *         @OA\Property(
     *           property="end_date",
     *           description="The sprint end date",
     *           type="string",
     *           format="date"
     *         )
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successfully updated",
     *     @OA\JsonContent(ref="#/components/schemas/Sprint"),
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not found"
     *   )
     *)
     **/
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sprint $sprint)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $sprint->update($request->all());
        return new SprintResource($sprint);
    }

    /**
     * @OA\Delete(
     **  path="/v1/sprints/{id}",
     *   summary="Delete a sprint",
     *   operationId="deleteSprint",
     *   tags={"Sprints"},
     *
     *   @OA\Parameter(
     *     description="ID of the sprint to delete",
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(
     *       type="integer",
     *       format="int64"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successfully deleted"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not found"
     *   )
     *)
     **/
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sprint $sprint)
    {
        $sprint->delete();
    }
}
