<div
x-data="{
    height:0,
    conversationElement:document.getElementById('conversation'),
}"
 x-init="
        height= conversationElement.scrollHeight;
        $nextTick(()=>conversationElement.scrollTop= height);

 "

 @scroll-bottom.window="
    $nextTick(()=>
    conversationElement.scrollTop= conversationElement.scrollHeight
    );
 "

class="w-full overflow-hidden">
    <div class="border-b flex flex-col overflow-y-scroll grow h-full">

    
        {{-- header --}}
        <header class="w-full sticky inset-x-0 flex pb-[5px] pt-[5px] top-0 z-10 bg-white border-b">
            <div class=" flex w-full items-center px-2 lg:px-4 gap-2 md:gap-5">
                <a class=" shrink-0 lg:hidden" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>
                </a>

                {{-- avatar --}}
                <div class="shrink-0">
                    <x-avatar class="h-9 w-9 lg:w-11 lg:h-11"/>
                </div>

                <h6 class="font-bold truncate">{{$selectedConversation->getReceiver()->email}}</h6>
            </div>

        </header>

        {{-- body --}}
        <main
         @scroll="
            scropTop = $el.scrollTop;
            if(scropTop <= 0){
                window.livewire.emit('loadMore');
            }
         "
         @update-chat-height.window="
            newHeight= $el.scrollHeight;
            oldHeight= height;
            $el.scrollTop= newHeight- oldHeight;
            height=newHeight;
         "






         id="conversation"
         class="flex flex-col gap-3 p-2.5 overflow-y-auto flex-grow overscroll-contain overflow-x-hidden w-full my-auto ">


        @if ($loadedMessages)

        @php
            $previousMessage= null;
        @endphp
            
        @foreach ($loadedMessages as $key=> $message)
            
        @if ($key>0)
            @php
                $previousMessage= $loadedMessages->get($key-1)
            @endphp
        @endif
        
            <div @class(['max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
                        'ml-auto'=>$message->sender_id===auth()->id(),
                ])>

                {{-- avatar --}}

                <div @class([
                    'shrink-0',
                    'invisible'=>$previousMessage?->sender_id==$message->sender_id,
                    'hidden'=>$message->sender_id ===auth()->id(),
                    ])>
                    <x-avatar />
                </div>

                <div @class(['flex flex-wrap text-[15px] rounded-xl p-2.5 flex flex-col text-black bg-[#j6j6j8jb]',
                        'rounded-bl-none bordre border-gray-200/40 bg-[#f6f6f8fb]'=>!($message->sender_id===auth()->id()),
                        'rounded-br-none bg-blue-500/80 text-white'=>($message->sender_id===auth()->id())
                    ])>

                    <p class=" whitespace-normal truncate text-sm md:text-base tracking-wide lg:tracking-normal">
                        {{$message->body}}
                    </p>

                    @if ($message->sender_id===auth()->id())
                        
                    
                    <div class="ml-auto flex gap-2">

                        <p @class([
                            'text-xs',
                            'text-gray-500'=>!($message->sender_id===auth()->id()),
                            'text-white'=>($message->sender_id===auth()->id()),

                        ])>
                            <!-- 5:25 am -->
                            {{$message->created_at->format('g:i a')}}
                        </p>

                        <div>

                        @if ($message->isRead())
                            {{-- double ticks --}}
                            <span @class(['text-gray-200'])>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                                    <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                    <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                </svg>
                            </span>
                        @else
                            {{-- single ticks --}}

                            <span @class(['text-gray-200'])>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                            </span>
                        @endif

                            

                            

                        </div>

                    </div>
                    @endif

                </div>

                
            </div>
            
        @endforeach
        @endif
        </main>

        {{-- send message --}}

        <footer class="shrink-0 z-10 bg-white inset-x-0">
            <div class=" p-2 border-t ">
                <form
                 x-data="{body:@entangle('body').defer}"
                 @submit.prevent="$wire.sendMessage"
                 method="POST" 
                 autocapitalize="off"
                >
                    @csrf
                    <input type="hidden" autocomplete="false" style="display: none;">
                    <div class="grid grid-cols-12 ">
                        <input 
                                x-model="body"
                                type="text"
                                autocomplete="off"
                                autofocus
                                placeholder="write your message here"
                                maxlength="1700"
                                class="col-span-10 bg-gray-100 border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg focus:outline"
                            >

                        <button x-bind:disabled="!body.trim()" class="col-span-2 " type="submit">Send</button>

                    </div>
                </form>

                @error('body')
                <p>{{$message}}</p>
                @enderror
            </div>

        </footer>
    </div>
</div>
