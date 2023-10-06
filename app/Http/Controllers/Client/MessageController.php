<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Client\MessageService;
use App\User;
use App\Dialog;
use App\Http\Requests\Client\Message\Create as MessageCreateRequest;
use App\Http\Requests\Client\Message\DeleteDialogs as MessageDeleteDialogsRequest;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function openDialog(User $user, Request $request)
    {
        return $this->messageService->openDialog($user, $request);
    }

    public function getDialogs(Request $request)
    {
        return $this->messageService->getDialogs($request);
    }

    public function createDialogMessage(Dialog $dialog, MessageCreateRequest $request)
    {
        return $this->messageService->createDialogMessage($dialog, $request);
    }

    public function getDialogMessages(Dialog $dialog, Request $request)
    {
        return $this->messageService->getDialogMessages($dialog, $request);
    }

    public function deleteDialogs(MessageDeleteDialogsRequest $request)
    {
        return $this->messageService->deleteDialogs($request);
    }

}
