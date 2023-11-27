<?php

namespace App\Http\Controllers;

use App\Exports\ParticipantExport;
use App\Models\Participant;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ParticipantController extends Controller
{

	public function export()
	{
		return Excel::download(new ParticipantExport(), 'users.xlsx');
	}
}
