<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use App\Services\CoreService;
use App\Repositories\MessageRepository;
use App\User;
use App\Dialog;
use App\Http\Resources\Client\Message\Message as MessageMessageResourse;
use App\Http\Resources\Client\Message\DialogWithMessages as MessageDialogWithMessagesResourse;
use App\Http\Resources\Client\Message\DialogListItem as MessageDialogListItemResourse;
use App\Events\Message\NewMessage as NewMessageEvent;
use App\Events\Message\GetDialogMessages as GetDialogMessagesEvent;

class MessageService extends CoreService
{
    protected $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function openDialog(User $user, Request $request)
    {
        $me = auth()->user();
        customThrowIf(($me->id == $user->id) || ! $user->isActive(), 'Wrong user');
        $dialog = $this->messageRepository->findDialogForUsers($me, $user);
        if( ! $dialog) {
            $dataDialog = [
                'from' => $me->id,
                'to' => $user->id,
            ];
            $dialog = $this->messageRepository->createDialog($dataDialog);
        } elseif ($dialog->is_deleted) {
            $dialog->update([
                'deleted_for' => null,
            ]);
        }

        return response()->result($dialog->id);
    }

    public function getDialogs(Request $request)
    {
        $me = auth()->user();
        $dialogs = $this->messageRepository->getUserDialogs($me);
        $dialogs->each(function($item) use($me){
            $item->setHasPaidUser();
            $item->setUserFor($me);
            if($item->lastMessage) {
                $item->lastMessage->my = $item->lastMessage->is_from ? $me->id == $item->from : $me->id == $item->to;
            }
        });

        return response()->result(MessageDialogListItemResourse::collection($dialogs));
    }

    public function createDialogMessage(Dialog $dialog, Request $request)
    {
        $me = auth()->user();
        customThrowIf( ! $dialog->isUser($me), 'Wrong dialog');
        $dialog->load(['sender', 'recipient']);
        $dataMessage = $request->validated();
        if($dialog->isFromUser($me)) {
            $dataMessage['is_from'] = true;
        }
        if( ! $dialog->isActive()) {
            $dialog = $this->messageRepository->updateDialog($dialog, ['active' => true]);
        }
        $dataMessage['is_paid'] = $dialog->hasPaidUser();
        
        $message = $this->messageRepository->createDialogMesage($dialog, $dataMessage);        
        $message->my = true;

        event(new NewMessageEvent($dialog, $message, $me));

        return response()->result(new MessageMessageResourse($message));
    }

    public function getDialogMessages(Dialog $dialog, Request $request)
    {
        $me = auth()->user();
        customThrowIf( ! $dialog->isUser($me), 'Wrong dialog');
        $isFrom = $dialog->from == $me->id;
        $dialog->load([
            'messages' => function($q) use($isFrom){
                $q->withMy($isFrom);
            },
            'sender' => function($q) use($me){
                $q->forDialog($me);
            },
            'recipient' => function($q) use($me){
                $q->forDialog($me);
            },
        ]);
        $dialog->setHasPaidUser();
        $dialog->setUserFor($me);

        event(new GetDialogMessagesEvent($dialog, $me));

        return response()->result(new MessageDialogWithMessagesResourse($dialog));
    }

    public function deleteDialogs(Request $request)
    {
        $me = auth()->user();
        $ids = $request->ids;
        $dialogs = Dialog::find($ids);
        customThrowIf($dialogs->count() <> count($ids), 'Wrong dilaogs');
        $dialogs->each(function($dialog) use($me){
            customThrowIf( ! $dialog->isUser($me), 'Wrong dialogs');
        });
        $this->messageRepository->deleteDialogs($dialogs, $me);

        return response()->result(true);
    }

}
