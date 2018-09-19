@extends('layouts.app')

@section('header')
    <link rel="stylesheet" type="text/css" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
    {{-- 绑定 data-locked 属性 --}}
    <thread-view :thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8" v-cloak>
                    
                    @include('threads._topic')

                    <replies @added="repliesCount++" @removed="repliesCount--"></replies>
                </div>

                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p>
                                <a href="#">{{ $thread->creator->name }}</a> 发布于 {{ $thread->created_at->diffForHumans() }}，当前共有 <span v-text="repliesCount"></span> 个回复。
                            </p>

                            <p>
                                <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}" v-if="signIn"></subscribe-button>

                                {{-- 增加 Lock 按钮 --}}
                                <button class="btn btn-default" v-if="authorize('isAdmin')" @click="toggleLock" v-text="locked ? 'Unlock' : 'Lock'"></button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection