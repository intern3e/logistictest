<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuzzyItem extends Model
{
    protected $table = 'fuzzy_item';
    protected $connection = 'pgsql';
    public $timestamps = false;

    protected $fillable = [
        'product_name',
        'group_id',
        'keyword',
        'item_name',
    ];

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Fuzzy search product_name ด้วย pg_trgm
     * FuzzyItem::fuzzy('Q6BAT')->get()
     */
    public function scopeFuzzy($query, string $search)
    {
        return $query->whereRaw("product_name % ?", [$search])
                     ->orderByRaw("similarity(product_name, ?) DESC", [$search]);
    }

    /**
     * ค้นด้วย group_id
     * FuzzyItem::byGroup(42)->get()
     */
    public function scopeByGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * ค้น keyword (ILIKE)
     * FuzzyItem::searchKeyword('NF30')->get()
     */
    public function scopeSearchKeyword($query, string $search)
    {
        return $query->where('keyword', 'ILIKE', "%{$search}%");
    }

    /**
     * ค้น item_name
     * FuzzyItem::searchItem('Q6BAT')->get()
     */
    public function scopeSearchItem($query, string $search)
    {
        return $query->whereRaw("item_name % ?", [$search])
                     ->orderByRaw("similarity(item_name, ?) DESC", [$search]);
    }

    /**
     * ดึง unique groups
     * FuzzyItem::uniqueGroups()->get()
     */
    public function scopeUniqueGroups($query)
    {
        return $query->selectRaw('DISTINCT ON (group_id) group_id, item_name, keyword')
                     ->orderBy('group_id');
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    public function soDetails()
    {
        return $this->hasMany(SoDetail::class, 'group_id', 'group_id');
    }
}