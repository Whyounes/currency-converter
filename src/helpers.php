<?php


if (!function_exists("throw_if")) {
    /**
     * @param bool $condition
     * @param \Throwable $exception
     */
    function throw_if(bool $condition, Throwable $exception)
    {
        if ($condition === true) {
            throw $exception;
        }
    }
}