<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'brand',
        'model_group',
        'gender',
        'description',
        'price',
        'original_price',
        'cost_price',
        'color_name',
        'color_hex',
        'is_featured',
        'is_active',
        'has_replacement_box',
        'packaging_notice',
        'fit_type',
        'fit_advice',
        'category_id',
        'images',
    ];

    const GENDER_MALE = 'hombre';
    const GENDER_FEMALE = 'mujer';
    const GENDER_KIDS = 'ninos';
    const GENDER_UNISEX = 'unisex';

    public static function getGenderOptions(): array
    {
        return [
            self::GENDER_MALE => 'Hombre',
            self::GENDER_FEMALE => 'Mujer',
            self::GENDER_KIDS => 'Niños',
            self::GENDER_UNISEX => 'Unisex',
        ];
    }

    public static function getFitOptions(): array
    {
        return [
            'regular' => 'Tallaje habitual (Fiel a la talla)',
            'runs_large' => 'Tallaje amplio / Grande (Recomendamos 1 talla menos)',
            'runs_small' => 'Tallaje ajustado / Pequeño (Recomendamos 1 talla más)',
            'oversize' => 'Corte Oversize / Holgado',
            'slim' => 'Corte Entallado / Slim Fit',
        ];
    }

    public function getFitAdviceText(): ?string
    {
        if (!empty($this->fit_advice)) {
            return $this->fit_advice;
        }

        return match ($this->fit_type) {
            'regular' => 'Tallaje estándar. Te recomendamos elegir tu talla habitual.',
            'runs_large' => 'Tallaje amplio. Si dudas entre dos tallas, te sugerimos elegir la menor.',
            'runs_small' => 'Tallaje reducido. Te recomendamos elegir una talla superior a la habitual.',
            'oversize' => 'Diseño de corte ancho y holgado (estilo oversize).',
            'slim' => 'Corte ceñido y entallado al cuerpo.',
            default => null,
        };
    }

    protected $casts = [
        'images' => 'array',
        'has_replacement_box' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Devuelve true si tiene al menos una variante con stock
     */
    public function hasStock(): bool
    {
        return $this->variants()->where('stock', '>', 0)->exists();
    }

    /**
     * Devuelve las variantes ordenadas inteligentemente (letras y números)
     */
    public function getSortedVariants()
    {
        $sizesOrder = [
            'XXXS' => 1, '3XS' => 1,
            'XXS' => 2, '2XS' => 2,
            'XS' => 3,
            'S' => 4,
            'M' => 5,
            'L' => 6,
            'XL' => 7,
            'XXL' => 8, '2XL' => 8,
            'XXXL' => 9, '3XL' => 9,
            'XXXXL' => 10, '4XL' => 10,
            'TU' => 11, 'ÚNICA' => 11, 'ONE SIZE' => 11,
        ];

        return $this->variants->sortBy(function ($variant) use ($sizesOrder) {
            $size = strtoupper(trim($variant->size));
            
            if (isset($sizesOrder[$size])) {
                return sprintf('%04d-00000000', $sizesOrder[$size]);
            }

            if (preg_match('/^(\d+(?:\.\d+)?)(?:\s+(\d+)\/(\d+))?/', $size, $matches)) {
                $base = (float)$matches[1];
                $fraction = 0;
                if (isset($matches[3]) && (float)$matches[3] > 0) {
                    $fraction = (float)$matches[2] / (float)$matches[3];
                }
                return sprintf('%04d-%08.4f', 100, $base + $fraction);
            }

            return sprintf('%04d-%s', 999, $size);
        });
    }
}
