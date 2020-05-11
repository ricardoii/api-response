<?php

namespace Obiefy\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Traits\Macroable;
use Obiefy\API\Contracts\APIResponseInterface;

class APIResponse implements APIResponseInterface
{
    use Macroable;

    /**
     * Status Label.
     *
     * @var string
     */
    protected $statusLabel;

    /**
     * Message Label.
     *
     * @var string
     */
    protected $messageLabel;

    /**
     * Data Label.
     *
     * @var string
     */
    protected $dataLabel;

    /**
     * Data count Label.
     *
     * @var string
     */
    public $dataCountLabel;

    public function __construct()
    {
        $this->setLabels();
    }

    /**
     * Register response labels.
     */
    public function setLabels()
    {
        $this->statusLabel = config('api.keys.status');
        $this->messageLabel = config('api.keys.message');
        $this->dataLabel = config('api.keys.data');
        $this->dataCountLabel = config('api.keys.data_count', 'DATA_COUNT');
    }

    /**
     * Create API response.
     *
     * @param int    $status
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function response($status = 200, $message = null, $data = [], ...$extraData)
    {
        $json = [
            $this->statusLabel  => config('api.stringify') ? strval($status) : $status,
            $this->messageLabel => $message,
            $this->dataLabel    => $data,
        ];

        is_countable($data) && config('api.include_data_count', false) && !empty($data) ?
            $json = array_merge($json, [$this->dataCountLabel => count($data)]) :
            '';

        if ($extraData) {
            foreach ($extraData as $extra) {
                $json = array_merge($json, $extra);
            }
        }

        return (config('api.match_status')) ? response()->json($json, $status) : response()->json($json);
    }

    /**
     * Create successful (200) API response.
     *
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function ok($message = null, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api.messages.success');
        }

        return $this->response(200, $message, $data, ...$extraData);
    }

    /**
     * Create Not found (404) API response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function notFound($message = null)
    {
        if (is_null($message)) {
            $message = config('api.messages.notfound');
        }

        return $this->response(404, $message, []);
    }

    /**
     * Create Validation (422) API response.
     *
     * @param string $message
     * @param array  $errors
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function validation($message = null, $errors = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api.messages.validation');
        }

        return $this->response(422, $message, $errors, ...$extraData);
    }

    /**
     * Create Validation (422) API response.
     *
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function forbidden($message = null, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api.messages.forbidden');
        }

        return $this->response(403, $message, $data, ...$extraData);
    }


    /**
     * Create Server error (500) API response.
     *
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function error($message = null, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api.messages.error');
        }

        return $this->response(500, $message, $data, ...$extraData);
    }
}
