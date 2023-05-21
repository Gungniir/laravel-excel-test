<?php

namespace App\Models;

use App\Events\RowSaved;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Row
 *
 * @property int $id
 * @property string $name
 * @property Carbon $date
 * @method static Builder|Row newModelQuery()
 * @method static Builder|Row newQuery()
 * @method static Builder|Row query()
 * @method static Builder|Row whereDate($value)
 * @method static Builder|Row whereId($value)
 * @method static Builder|Row whereName($value)
 * @property int $file_id
 * @method static Builder|Row whereFileId($value)
 * @mixin Eloquent
 */
class Row extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $casts = [
        'date' => 'date'
    ];
    protected $fillable = [
        'id',
        'name',
        'file_id',
        'date',
    ];
}
