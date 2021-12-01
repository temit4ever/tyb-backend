<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="YTB Backend",
 *      description="Backend API for the YTB Project",
 *      @OA\Contact(
 *          email="cliff@interfolio.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * @OA\Schema(
 *     schema="timestamps",
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="ErrorItem",
 *     type="object",
 *     @OA\Property(property="field", type="array", @OA\Items(type="string")),
 *     example={"title": "The title field is required."}
 * )
 * @OA\Schema(
 *   schema="ValidationError",
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="errors", type="array", @OA\Items(ref="#/components/schemas/ErrorItem")),
 *     example={"message": "The given data was invalid", "errors": {"title": "The title field is required."}}
 * )

 *
 *
 */

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
