@extends('frontend.layouts.guest')

@section('content')

<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1>Events</h1>

                <ul class="ritekhela-breadcrumb">

                    <li><a href="{{url('/home')}}">Home</a></li>

                    <li>Events</li>



                </ul>

            </div>

        </div>

    </div>

</div>

<div class="ritekhela-main-content">

    <div class="ritekhela-main-section ritekhela-fixture-list-full">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="ritekhela-fixture ritekhela-modren-fixture">



                        <div class="ritekhela-main-section ritekhela-fixture-list-full">

                            <div class="container">

                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="ritekhela-blogs ritekhela-blog-view1">

                                            <ul class="row">

                                                @if ($events->count()>0)
                                                @foreach ($events as $event)
                                                <li class="col-md-4">

                                                    <figure><a href="{{ route('EventsDetail',$event->id) }}"><img src="{{ asset(upload_url( 'events/'.$event->id.'/'.$event->banner) )}}" alt="" style="height: 12rem"> </a></figure>

                                                    <div class="ritekhela-blog-view1-text">

                                                        <ul class="ritekhela-blog-options">

                                                            <li><i class="far fa-calendar-alt"></i> <?php echo date('m/d/Y', $event->start_date_time); ?> </li>

                                                            <li>

                                                                <i class="fa fa-users"></i>


                                                                {{ get_age_title($event->age_group) .' Years' }}

                                                            </li>

                                                        </ul>

                                                        <h2><a href="{{ route('EventsDetail',$event->id) }}"><?php echo $event->title; ?></a></h2>

                                                        <h6><i class="fa fa-user"></i> <?php echo " " . get_user_name($event->user_id); ?></h6>

                                                        <p><?php echo $event->description; ?></p>

                                                        <a href="{{ route('EventsDetail',$event->id) }}" class="ritekhela-blog-view1-btn">Read More</a>

                                                    </div>

                                                </li>
                                                @endforeach
                                                @else
                                                <div class="col-lg-12" style="text-align: center">

                                                    <h2>No Events Available yet</h2>

                                                </div>
                                                @endif


                                            </ul>

                                        </div>

                                        <div class="ritekhela-pagination">

                                            <ul>
                                                {{ $events->links() }}



                                            </ul>

                                        </div>

                                    </div>



                                </div>

                            </div>

                        </div>



                    </div>

                </div>



            </div>

        </div>

    </div>


</div>


@endsection
