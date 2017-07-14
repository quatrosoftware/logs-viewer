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

    <style>
        #page {
            margin-top: 70px;
        }

        .navbar-brand small {
            font-size: 13px;
            display: block;
            color: silver;
            margin-top: 6px;
        }

        .navbar {
            height: 65px;
        }

        .container {
            width: 100%;
        }

        .alert-margin {
            margin-top: 55px;
        }

    </style>
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{route($route)}}">Laravel Logs Viewer
                <small>By J.Socha &copy; 2017</small>
            </a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <a href="#"></a>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">

    <div id="page">

        <div class="row">
            <div class="col-md-2">
                <h3 class="text-uppercase">Files:</h3>
                @if(count($allLogs) ==0)
                    <div class="alert alert-info">No log files found</div>
                @else
                    <form>
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>File</th>
                                <th width="10"><input type="checkbox" id="checker"/></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($allLogs as $file)
                                <tr>
                                    <td><a href="{{route($route, ['file' => base64_encode($file)])}}"> {{$file}}</a></td>
                                    <td><input type="checkbox" class="pull-right checkbox" name="delete[]" value="{{base64_encode($file)}}"/></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="text-center">
                            <input type="hidden" name="action" value="deleteMultiple"/>
                            <button class="btn btn-sm btn-danger" type="submit"><i class="fa fa-trash"></i> Delete selected</button>
                        </div>
                    </form>
                @endif

            </div>
            <div class="col-md-9">
                @if(!is_null($message))
                    <div class="alert alert-success alert-margin">{!! $message !!}</div>
                @else
                    @if(is_null($log['name']))
                        <div class="alert alert-info alert-margin">Select log file from sidebar</div>
                    @else
                        <h3>
                            <a href="{{route($route, ['file' => base64_encode($log['name']), 'action' => 'delete'])}}"> <span class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></span></a>
                            <span class="text-uppercase"> File:</span> {{$log['name']}}
                            <small>(Total: {{array_sum($log['unique'])}}, Unical: {{count($log['logs'])}})</small>
                        </h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Log</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($log['logs'] as $key => $item)
                                <tr>
                                    <td width="50" class="text-center"><span class="btn btn-warning btn-xs">{{$log['unique'][$item{'hash'}]}}x</span></td>
                                    <td width="175"><b>{{Carbon\Carbon::parse($item['date'])->toTimeString()}}</b> <br/>
                                        <small>{{\Carbon\Carbon::parse($item['date'])->diffForHumans()}}</small>
                                    </td>
                                    <td> {!!nl2br($item['error'])!!} <br/>
                                        <div id="log_{{$key}}" class="hidden">
                                            <small>
                                                {!!nl2br($item['stack'])!!}
                                            </small>
                                        </div>
                                    </td>
                                    <td width="50">
                                        @if(strlen($item['stack']) > 5)
                                            <span class="btn btn-sm btn-info log-show" data-id="{{$key}}"><i class="fa fa-search"></i></span>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>

                    @endif
                @endif

            </div>
        </div>
    </div>

</div><!-- /.container -->


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

    $('#checker').on('click', function () {
        $('.checkbox').prop('checked', $(this).is(':checked'));
    })

</script>
</body>

</html>