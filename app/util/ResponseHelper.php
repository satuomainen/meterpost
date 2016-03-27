<?php

use Illuminate\Http\Response;

class ResponseHelper {
    public static function okResponse() {
        return new Response(
            "OK",
            Response::HTTP_OK,
            static::getResponseHeaders()
        );
    }

    public static function badRequestResponse() {
        return new Response(
            "Bad request",
            Response::HTTP_BAD_REQUEST,
            static::getResponseHeaders()
        );
    }

    public static function notFoundResponse() {
        return new Response(
            "Not found",
            Response::HTTP_NOT_FOUND,
            static::getResponseHeaders()
        );
    }

    public static function unauthorizedResponse() {
        return new Response(
            "Unauthorized",
            Response::HTTP_UNAUTHORIZED,
            static::getResponseHeaders()
        );
    }

    public static function notImplementedResponse() {
        return new Response(
            "Not implemented",
            Response::HTTP_NOT_IMPLEMENTED,
            static::getResponseHeaders()
        );
    }

    private static function getResponseHeaders() {
        return array('content-type' => 'text/html');
    }
}