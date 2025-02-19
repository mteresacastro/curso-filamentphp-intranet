<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id','desc')->first();
        if ($lastTimesheet == null){
            return [
                Action::make('inWork')
                ->label('Start working')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (){
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                }),
                Actions\CreateAction::make(),
            ];
        }
        return [
            //Para crear botones de acciones
            Action::make('inWork')
            ->label('Start working')
            ->color('success')
            //->keyBindings(['command+s','ctrl+s']) para añadir comando a la accion
            ->requiresConfirmation()
            //Para decirle que acción queremos que haga el botón
            ->visible(!$lastTimesheet->day_out == null)
            ->disabled($lastTimesheet->day_out == null)
            ->action(function (){
                $user = Auth::user();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->type = 'work';
                $timesheet->save();

                Notification::make()
                ->title('Has comenzado a trabajar ¡Buen día!')
                ->body('Has comenzado a trabajar a las'.' '.Carbon::now()->format('H:i'))
                ->success()
                ->send();
            }),
            Action::make('stopWork')
            ->label('Stop working')
            ->color('success')
            ->requiresConfirmation()
            ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
            ->disabled(!$lastTimesheet->day_out == null)
            ->action(function () use($lastTimesheet){
                $lastTimesheet->day_out = Carbon::now();
                $lastTimesheet->save();

                Notification::make()
                ->title('Has finalizado de trabajar ¡Hasta pronto!')
                ->success()
                ->send();
            }),
            Action::make('inPause')
            ->label('Start pause')
            ->color('warning')
            ->requiresConfirmation()
            ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
            ->disabled(!$lastTimesheet->day_out == null)
            ->action(function () use($lastTimesheet){
                $lastTimesheet->day_out = Carbon::now();
                $lastTimesheet->save();
                $user = Auth::user();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->type = 'pause';
                $timesheet->save();

                //Notificaciones modales
                Notification::make()
                ->title('Has comenzado la pausa')
                ->warning()
                ->color('warning')
                ->send();
            }),
            Action::make('stopPause')
            ->label('Stop pause')
            ->color('warning')
            ->requiresConfirmation()
            ->visible($lastTimesheet->day_out == null && $lastTimesheet->type == 'pause')
            ->disabled(!$lastTimesheet->day_out == null)
            ->action(function () use($lastTimesheet){
                $lastTimesheet->day_out = Carbon::now();
                $lastTimesheet->save();
                $user = Auth::user();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->type = 'work';
                $timesheet->save();

                Notification::make()
                ->title('Has finalizado la pausa')
                ->warning()
                ->color('warning')
                ->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
