@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')

<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">ASSIGNED SUBJECT</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Assigned</a></li>
                    <li class="breadcrumb-item active"> Subject</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->


<div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
        
                                        <h4 class="card-title"> Assigned - Subject </h4>

        
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Class</th>
                                                <th>Subject</th>
                                                <!--<th>Term</th>
                                                <th>Session</th>
-->
                                                
                                            
                                            </tr>
                                            </thead>
        
        
                                            <tbody>
                                                @foreach($assignments as $key => $assignment)

                                            <tr><b>
                                                <td><b>{{ $key+1 }}</b></td>
                                                <td><b>{{ $assignment->class->class_name }}</b></td>
                                                <td><b>{{ $assignment->subject->subject_name }}</b></td>
                                                <!--<td><b> {{ $assignment->term }}</b> </td>
                                                <td> <b>{{ $assignment->session }}</b> </td>
-->
                                            </tr>
                                            

                                                @endforeach

                                            </tr>
                                            </tbody>
                                        </table>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->





@endsection