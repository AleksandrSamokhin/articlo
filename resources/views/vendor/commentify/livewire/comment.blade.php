<div>
    @if($isEditing)
        @include('commentify::livewire.partials.comment-form',[
            'method'=>'editComment',
            'state'=>'editState',
            'inputId'=> 'reply-comment',
            'inputLabel'=> __('commentify::commentify.comments.your_reply'),
            'button'=> __('commentify::commentify.comments.edit_comment')
        ])
    @else
        <article class="p-6 mb-1 text-base bg-white rounded-lg dark:bg-slate-900">
            <footer class="flex justify-between items-center mb-1">
                <div class="flex items-center">
                    <p class="inline-flex items-center mr-3 text-sm text-slate-900 dark:text-white">
                        <img class="mr-2 w-6 h-6 rounded-full" src="{{$comment->user->avatar()}}" alt="{{$comment->user->name}}">
                        {{Str::ucfirst($comment->user->name)}}
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        <time pubdate datetime="{{$comment->presenter()->relativeCreatedAt()}}" title="{{$comment->presenter()->relativeCreatedAt()}}">
                            {{$comment->presenter()->relativeCreatedAt()}}
                        </time>
                    </p>
                </div>
                <div class="relative">
                    <button wire:click="$toggle('showOptions')" class="inline-flex items-center p-2 text-sm font-medium text-center text-slate-400 bg-white rounded-lg hover:bg-slate-100 focus:ring-4 focus:outline-none focus:ring-slate-50 dark:bg-slate-900 dark:hover:bg-slate-700 dark:focus:ring-slate-600" type="button">
                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </button>
                    @if($showOptions)
                        <div class="absolute z-10 top-full right-0 mt-1 w-36 bg-white rounded divide-y divide-slate-100 shadow dark:bg-slate-700 dark:divide-slate-600">
                            <ul class="py-1 text-sm text-slate-700 dark:text-slate-200">
                                @can('update',$comment)
                                    <li>
                                        <button wire:click="$toggle('isEditing')" type="button" class="block w-full text-left py-2 px-4 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                            {{ __('commentify::commentify.comments.edit') }}
                                        </button>
                                    </li>
                                @endcan
                                @can('destroy',$comment)
                                    <li>
                                        <button x-on:click="confirmCommentDeletion" x-data="{ confirmCommentDeletion(){ if(window.confirm('{{ __('commentify::commentify.comments.delete_confirm') }}')){ @this.call('deleteComment') } } }" class="block w-full text-left py-2 px-4 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                            {{ __('commentify::commentify.comments.delete') }}
                                        </button>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    @endif
                </div>
            </footer>
            <p class="text-slate-500 dark:text-slate-400">
                {!! $comment->presenter()->replaceUserMentions($comment->presenter()->markdownBody()) !!}
            </p>
            <div class="flex items-center mt-4 space-x-4">
                <livewire:like :$comment :key="$comment->id"/>
                @include('commentify::livewire.partials.comment-reply')
            </div>
        </article>
    @endif
    @if($isReplying)
        @include('commentify::livewire.partials.comment-form',[
           'method'=>'postReply',
           'state'=>'replyState',
           'inputId'=> 'reply-comment',
           'inputLabel'=> __('commentify::commentify.comments.your_reply'),
           'button'=> __('commentify::commentify.comments.post_reply')
       ])
    @endif
    @if($hasReplies)
        <article class="p-1 mb-1 ml-1 lg:ml-12 border-t border-slate-200 dark:border-slate-700 dark:bg-slate-900">
            @foreach($comment->children as $child)
                <livewire:comment :comment="$child" :key="$child->id"/>
            @endforeach
        </article>
    @endif
</div>


