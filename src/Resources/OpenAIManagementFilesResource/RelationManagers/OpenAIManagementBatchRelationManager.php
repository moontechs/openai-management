<?php

namespace Moontechs\OpenAIManagement\Resources\OpenAIManagementFilesResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Moontechs\OpenAIManagement\Actions\DownloadProcessedFileAction;
use Moontechs\OpenAIManagement\Forms\CreateBatchForm;
use Moontechs\OpenAIManagement\Models\OpenAIManagementBatch;

class OpenAIManagementBatchRelationManager extends RelationManager
{
    protected static string $relationship = 'batches';

    protected static ?string $modelLabel = 'OpenAI Batch';

    public function form(Form $form): Form
    {
        return $form->schema(CreateBatchForm::getFormSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('endpoint')
            ->columns([
                Tables\Columns\TextColumn::make('endpoint'),
                Tables\Columns\TextColumn::make('batch_data.status')
                    ->label('Batch status')
                    ->badge()
                    ->color(fn (OpenAIManagementBatch $record) => match ($record?->batch_data['status']) {
                        'completed' => 'success',
                        'error' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                DownloadProcessedFileAction::make(),
                Tables\Actions\ViewAction::make()->visible(fn ($record) => $record->batch_data !== null),
                Tables\Actions\EditAction::make()->visible(fn ($record) => $record->batch_data === null),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
