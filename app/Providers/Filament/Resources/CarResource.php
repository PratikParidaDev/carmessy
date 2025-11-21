<?php
use App\Providers\Filament\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Listings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('make_id')
                            ->relationship('make', 'name')
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('model_id')
                            ->relationship('model', 'name')
                            ->required(),
                        Forms\Components\Select::make('dealer_id')
                            ->relationship('dealer', 'business_name')
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 1),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\Select::make('condition')
                            ->options([
                                'new' => 'New',
                                'used' => 'Used',
                                'certified' => 'Certified',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('mileage')
                            ->numeric()
                            ->suffix('km'),
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'petrol' => 'Petrol',
                                'diesel' => 'Diesel',
                                'electric' => 'Electric',
                                'hybrid' => 'Hybrid',
                                'cng' => 'CNG',
                                'lpg' => 'LPG',
                            ])
                            ->required(),
                        Forms\Components\Select::make('transmission')
                            ->options([
                                'manual' => 'Manual',
                                'automatic' => 'Automatic',
                                'semi-automatic' => 'Semi-Automatic',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'sold' => 'Sold',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified'),
                        Forms\Components\DateTimePicker::make('published_at'),
                        Forms\Components\Textarea::make('admin_notes')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->getStateUsing(fn ($record) => $record->getFirstMediaUrl('images'))
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('make.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('dealer.business_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'sold',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'sold' => 'Sold',
                    ]),
                Tables\Filters\SelectFilter::make('make')
                    ->relationship('make', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}





?>