<?php

namespace App\Services;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

class DeletionService
{
    public static function delete($model, $id, $type)
    {
        if ($id > 0) {
            $item = $model::find($id);

            if (!$item) {
                return self::errorResponse($type . ' not found.');
            }

            if ($item->delete()) {
                return self::successResponse($type . ' deleted successfully.');
            }

            return self::errorResponse($type . ' couldn\'t be deleted.');
        } else {
            return self::errorResponse('Cannot delete the default ' . $type . '.');
        }
    }

    private static function successResponse($message)
    {
        Session::flash('message', $message);
        return response()->json(['error' => false, 'message' => $message]);
    }

    private static function errorResponse($message)
    {
        Session::flash('error', $message);
        return response()->json(['error' => true, 'message' => $message]);
    }
}
