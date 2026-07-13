<?php

namespace App\Models;

use App\Models\Catalog\AnalysisArea;
use App\Models\Catalog\LabelColor;
use App\Models\Catalog\LabMatrix;
use App\Models\Catalog\MeasurementUnit;
use App\Models\Catalog\Methodology;
use App\Models\Catalog\QuoteRemark;
use App\Models\Catalog\SampleContainer;
use App\Models\Catalog\SamplePreserver;
use App\Models\Catalog\SampleStorage;
use App\Models\Catalog\SamplingRemark;
use App\Models\Parameters\Group;
use App\Models\Traits\Blamable;
use App\Models\Traits\SerializeDate;
use App\Models\Traits\Versioning;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parameter extends Model
{
    use SoftDeletes, Blamable, SerializeDate, Versioning;

    protected $fillable = [
        'name',
        'price',
        'unit_volume',
        'group_volume',
        'measurement_unit_id',
        'lab_matrix_id',
        'methodology_id',
        'sample_container_id',
        'label_color_id',
        'parameter_group_id',
        'parameter_category_id',
        'sample_preserver_id',
        'analysis_area_id',
        'sample_storage_id',
        'multiple',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'multiple' => 'boolean'
    ];

    public function labMatrix(): BelongsTo
    {
        return $this->belongsTo(LabMatrix::class)->select('id', 'name', 'code');
    }

    public function measurementUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class)->select('id', 'unit');
    }

    public function methodology(): BelongsTo
    {
        return $this->belongsTo(Methodology::class)->select('id', 'name');
    }

    public function sampleContainer(): BelongsTo
    {
        return $this->belongsTo(SampleContainer::class)->select('id', 'name');
    }

    public function samplePreserver(): BelongsTo
    {
        return $this->belongsTo(SamplePreserver::class)->select('id', 'name');
    }

    public function analysisArea(): BelongsTo
    {
        return $this->belongsTo(AnalysisArea::class)->select('id', 'name', 'code');
    }

    public function labelColor(): BelongsTo
    {
        return $this->belongsTo(LabelColor::class)->select('id', 'color');
    }

    public function sampleStorage(): BelongsTo
    {
        return $this->belongsTo(SampleStorage::class)->select('id', 'identifier');
    }

    public function quoteRemarks(): MorphToMany
    {
        return $this->morphedByMany(QuoteRemark::class, 'remarkable');
    }

    public function samplingRemarks(): MorphToMany
    {
        return $this->morphedByMany(SamplingRemark::class, 'remarkable');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parameter_group_id', 'id');
    }
}
