<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos'),
            'legal' => Tab::make('Legal y Contacto')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'legal')),
            'marketing' => Tab::make('Marketing y Portada')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'marketing')),
            'tienda' => Tab::make('Reglas de Tienda')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'tienda')),
            'sistema' => Tab::make('Sistema')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'sistema')),
        ];
    }
}
