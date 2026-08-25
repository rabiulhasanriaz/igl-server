@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_tree_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Reseller Graph</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Reseller
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Tree & Graph
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div id="test" class="tree">
                <h3 class="text-center text-primary">Tree Of Customer</h3>
                <hr>
                <ul>
                    @foreach($roots as $root)
                        <li class="parent_li">
                            <span title="Root" style="background: green; color: #fff;">{{ @$root->userDetail->company_name }}</span>
                            <ul>

                                <!-- main -reseller -->
                                @if($root->myUsers->count()>'0')
                                    @foreach($root->myUsers as $user1)
                                        <li class="parent_li">
                                            <span title="IGL Web Lmt"
                                                  style="color: green;font-weight: bold;">{{ @$user1->company_name }}</span>
                                            <ul>
                                                <!-- 2nd -reseller -->
                                                @if($user1->myUsers->count()>'0')
                                                    @foreach($user1->myUsers as $user2)
                                                        <li class="parent_li">
                                                            <span title="IGL Web Lmt"
                                                                  style="color: #77017e;font-size: 12px; ">{{ @$user2->company_name }}</span>
                                                            <ul>
                                                                <!-- 3rd reseller-->
                                                                @if($user2->myUsers->count()>'0')
                                                                    @foreach($user2->myUsers as $user3)
                                                                        <li class="parent_li">
                                                                    <span title="IGL Web Lmt"
                                                                          class="text-primary">{{ @$user3->company_name }}</span>
                                                                    <ul>
                                                                        <!-- 4th reseller -->
                                                                        @if($user3->myUsers->count()>'0')
                                                                            @foreach($user3->myUsers as $user4)
                                                                                <li class="parent_li">
                                                                                    <span title="IGL Web Lmt" class="text-primary">{{ @$user4->company_name }}</span>
                                                                                    <ul>
                                                                                        <!-- 5th reseller -->
                                                                                        @if($user4->myUsers->count()>'0')
                                                                                            @foreach($user4->myUsers as $user5)
                                                                                                <li class="parent_li">
                                                                                                    <span title="IGL Web Lmt" class="text-danger">{{ @$user5->company_name }}</span>
                                                                                                    <ul>
                                                                                                        <!--last users-->
                                                                                                        @if($user5->myUsers->count()>'0')
                                                                                                            @foreach($user5->myUsers as $user6)
                                                                                                        <li class="parent_li">
                                                                                                            <span title="IGL Web Lmt" class="text-danger">{{ @$user6->company_name }}</span>
                                                                                                        </li>
                                                                                                            @endforeach
                                                                                                        @endif
                                                                                                    </ul>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </ul>
                                                                                </li>
                                                                            @endforeach
                                                                        @endif
                                                                    </ul>
                                                                </li>
                                                                    @endforeach
                                                                @endif
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                @endif

                                            </ul>
                                        </li>
                                @endforeach
                            @endif
                            <!-- main -reseller end -->

                            </ul>
                        </li>
                    @endforeach

                </ul>


            </div>

        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection