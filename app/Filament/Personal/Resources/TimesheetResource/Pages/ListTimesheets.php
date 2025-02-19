<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Para crear botones de acciones
            Action::make('inWork')
            ->label('Start working')
            ->color('success')
            //->keyBindings(['command+s','ctrl+s']) para añadir comando a la accion
            ->requiresConfirmation()
            //Para decirle que acción queremos que haga el botón
            ->action(function (){
                $user = Auth::user();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->day_out = Carbon::now();
                $timesheet->type = 'work';
                $timesheet->save();
            }),
            Action::make('inPausek')
            ->label('Stop working')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (){
                $user = Auth::user();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->day_out = Carbon::now();
                $timesheet->type = 'pause';
                $timesheet->save();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
