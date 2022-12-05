<?php

namespace AhsanDev\Support\Livewire;

trait Flashable
{
    /**
     * Trigger the livewire emit with flash message.
     *
     * @return void
     */
    protected function flash($message, $type = 'success')
    {
        if (is_array($message)) {
            $data = [
                'message' => $message['message'] ?? 'No Message!',
                'type' => $message['type'] ?? 'success',
                'duration' => $message['duration'] ?? null,
                'redirect' => $message['redirect'] ?? null,
            ];

            if (isset($message['session'])) {
                session(['flash_message' => $message['message']]);
            }
        } else {
            $data = compact('message', 'type');
        }

        $this->emit('flash', $data);
    }
}
