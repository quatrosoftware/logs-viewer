<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Laravel Logs Viewer</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="{{asset('vendor/logs-viewer/sb-admin-2.min.css')}}" rel="stylesheet"/>

    <link href="{{asset('vendor/logs-viewer/metisMenu.min.css')}}" rel="stylesheet">
</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{route($route)}}">Laravel Logs Viewer
                <small>by Verti</small>
            </a>
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">


            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">

                    @if(count($logs) ==0)
                        <div class="alert alert-info">No log files found</div>
                    @endif

                    @foreach($logs as $log)
                        <li>
                            <a href="{{route($route, ['file' => base64_encode($log)])}}"> <i class="fa fa-chevron-right"></i> {{$log}}</a>
                        </li>

                    @endforeach


                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12" style="padding: 10px;">

                    @if($logName == '')
                        <div class="alert alert-info">Select log file from sidebar</div>
                    @else
                        <h3>
                            <a href="{{route($route, ['file' => base64_encode($logName), 'action' => 'delete'])}}"> <span class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></span></a>
                            File: {{$logName}}
                            <small>({{count($detailedLog)}} logs)</small>
                        </h3>
                        <table class="table table-striped table-bordered">
                            <thead>

                            </thead>
                            <tbody>
                            @foreach($detailedLog as $key => $log)
                                <tr>
                                    <td width="175"><b>{{$log['date']}}</b> <br/>
                                        <small>{{\Carbon\Carbon::parse($log['date'])->diffForHumans()}}</small>
                                    </td>
                                    <td width="50" class="text-center">{!! $log['icon'] !!}</td>
                                    <td> {!!nl2br($log['error'])!!}<br/>
                                        <div id="log_{{$key}}" class="hidden">
                                            <small>
                                                {!!nl2br($log['stack'])!!}
                                            </small>
                                        </div>
                                    </td>
                                    <td width="50">
                                        <span class="btn btn-sm btn-info log-show" data-id="{{$key}}"><i class="fa fa-search"></i></span>
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>

                    @endif
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>

    $('.log-show').on('click', function () {
        var logId = $(this).attr('data-id');
        $('#log_' + logId).removeClass('hidden');
    });


</script>
</body>

</html>