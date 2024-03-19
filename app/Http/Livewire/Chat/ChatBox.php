<?php

namespace App\Http\Livewire\Chat;

use App\Models\Message;
use Livewire\Component;

class ChatBox extends Component
{
    // :selectedConversation="$selectedConversation"
    public $selectedConversation;
    public $body;
    public $loadedMessages;
    public $paginate_var=10;

    protected $listeners=['loadMore'];


    public function loadMore() : void {
        // dd('scroll Top');
        $this->paginate_var += 10;
        $this->loadMessages();
        $this->dispatchBrowserEvent('update-chat-height');
    }
    
    public function loadMessages() {

        $count=Message::where('conversation_id',$this->selectedConversation->id)->count();

        $this->loadedMessages=Message::where('conversation_id',$this->selectedConversation->id)
                                    ->skip($count-$this->paginate_var)
                                    ->take($this->paginate_var)
                                    ->get();
        return $this->loadedMessages;
    }

    public function sendMessage() {
        $this->validate(['body'=>'required|string']);
        $createdMessage= Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'body' => $this->body,
        ]);
        $this->reset('body');
        $this->dispatchBrowserEvent('scroll-bottom');

        //push the message
        $this->loadedMessages->push($createdMessage);

        //update conversation model
        $this->selectedConversation->updated_at= now();
        $this->selectedConversation->save();

        //refresh chatlist
        $this->emitTo('chat.chat-list','refresh');
    }

    function mount() {
        $this->loadMessages();
    }
    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
