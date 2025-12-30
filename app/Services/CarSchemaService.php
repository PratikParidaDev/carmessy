<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class CarSchemaService
{
    protected $tableName = 'cars';
    
    /**
     * Fields that should be hidden from users (admin-only)
     */
    protected $restrictedFields = [
        'id',
        'slug',
        'dealer_id',
        'status',
        'is_featured',
        'is_verified',
        'admin_notes',
        'meta_title',
        'meta_description',
        'views',
        'inquiries',
        'featured_until',
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Fields that should be hidden in create form (business rules)
     */
    protected $hiddenInCreate = [
        'condition', // Always set to 'used' by business rule
    ];

    /**
     * Fields that should appear in the listing table
     */
    protected $listingFields = [
        'title',
        'make_id',
        'model_id',
        'year',
        'price',
        'condition',
        'status',
        'created_at',
    ];

    /**
     * Get all editable fields from the schema
     */
    public function getEditableFields(bool $forCreate = false): array
    {
        $columns = Schema::getColumnListing($this->tableName);
        $fields = [];

        foreach ($columns as $column) {
            if (in_array($column, $this->restrictedFields)) {
                continue;
            }

            // Hide fields that should not appear in create form
            if ($forCreate && in_array($column, $this->hiddenInCreate)) {
                continue;
            }

            $fields[$column] = $this->getFieldDefinition($column);
        }

        return $fields;
    }

    /**
     * Get fields that should appear in the listing table
     */
    public function getListingFields(): array
    {
        $columns = Schema::getColumnListing($this->tableName);
        $fields = [];

        foreach ($this->listingFields as $fieldName) {
            if (in_array($fieldName, $columns)) {
                $fields[$fieldName] = $this->getFieldDefinition($fieldName, true);
            }
        }

        return $fields;
    }

    /**
     * Get field definition based on column type
     */
    protected function getFieldDefinition(string $column, bool $forListing = false): array
    {
        $columnType = $this->getColumnType($column);
        $definition = [
            'name' => $column,
            'label' => $this->getFieldLabel($column),
            'type' => $this->determineInputType($column, $columnType),
            'required' => $this->isRequired($column),
            'nullable' => $this->isNullable($column),
        ];

        // Add type-specific attributes
        switch ($definition['type']) {
            case 'select':
                $definition['options'] = $this->getEnumOptions($column);
                break;
            case 'number':
                $definition['min'] = $this->getMinValue($column);
                $definition['max'] = $this->getMaxValue($column);
                $definition['step'] = $this->getStepValue($column);
                break;
            case 'textarea':
                $definition['rows'] = $this->getTextareaRows($column);
                break;
            case 'file':
                $definition['multiple'] = true;
                $definition['accept'] = 'image/*';
                break;
            case 'multi-select':
                // JSON fields that should be multi-select
                $definition['options'] = $this->getMultiSelectOptions($column);
                break;
            case 'date':
                // Date fields
                break;
            case 'boolean':
                // Boolean fields (checkbox)
                break;
        }

        // For listing, add display format
        if ($forListing) {
            $definition['display'] = $this->getDisplayFormat($column);
        }

        return $definition;
    }

    /**
     * Get column type from database
     */
    protected function getColumnType(string $column): string
    {
        try {
            $connection = DB::connection();
            $doctrineColumn = $connection->getDoctrineColumn($this->tableName, $column);
            return $doctrineColumn->getType()->getName();
        } catch (\Exception $e) {
            // Fallback to string if we can't determine the type
            return 'string';
        }
    }

    /**
     * Determine input type based on column type and name
     */
    protected function determineInputType(string $column, string $columnType): string
    {
        // Check for specific field names first
        if ($column === 'images' || str_contains($column, 'image')) {
            return 'file';
        }

        if ($column === 'description' || str_contains($column, 'description') || 
            str_contains($column, 'history') || str_contains($column, 'notes')) {
            return 'textarea';
        }

        // Check for foreign key fields (ending with _id) - these should be selects
        if (str_ends_with($column, '_id') && in_array($column, ['make_id', 'model_id', 'city_id', 'dealer_id'])) {
            return 'select';
        }

        // Check for enum types
        if ($this->isEnum($column)) {
            return 'select';
        }

        // Check for JSON types (features, safety_features)
        if ($columnType === 'json' || in_array($column, ['features', 'safety_features'])) {
            return 'multi-select';
        }

        // Check for boolean types
        if ($columnType === 'boolean' || str_starts_with($column, 'is_') || 
            str_contains($column, '_valid') || str_contains($column, 'under_')) {
            return 'boolean';
        }

        // Check for date types
        if ($columnType === 'date' || str_contains($column, 'date') || 
            str_contains($column, '_expiry') || str_contains($column, '_at')) {
            return 'date';
        }

        // Check for numeric types
        if (in_array($columnType, ['integer', 'bigint', 'smallint', 'tinyint']) ||
            in_array($column, ['year', 'mileage', 'power', 'torque', 'owners', 'seats', 'doors'])) {
            return 'number';
        }

        // Check for decimal types
        if ($columnType === 'decimal' || $columnType === 'float' || 
            in_array($column, ['price', 'mileage_kmpl'])) {
            return 'number';
        }

        // Default to text
        return 'text';
    }

    /**
     * Check if column is an enum
     */
    protected function isEnum(string $column): bool
    {
        // Known enum columns from migration
        $enumColumns = ['condition', 'fuel_type', 'transmission', 'status'];
        
        if (in_array($column, $enumColumns)) {
            return true;
        }

        // Try to detect from database
        try {
            $connection = DB::connection();
            $doctrineColumn = $connection->getDoctrineColumn($this->tableName, $column);
            $type = $doctrineColumn->getType()->getName();
            
            // Check if it's an enum type (varies by database driver)
            return str_contains(strtolower($type), 'enum') || 
                   $doctrineColumn->getType() instanceof \Doctrine\DBAL\Types\StringType;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get enum options from database
     */
    protected function getEnumOptions(string $column): array
    {
        // Get enum values from migration or database
        $enumValues = [
            'condition' => ['new', 'used', 'certified'],
            'fuel_type' => ['petrol', 'diesel', 'electric', 'hybrid', 'cng', 'lpg'],
            'transmission' => ['manual', 'automatic', 'semi-automatic'],
            'status' => ['pending', 'approved', 'rejected', 'sold'],
        ];

        return $enumValues[$column] ?? [];
    }

    /**
     * Get multi-select options (for JSON fields)
     */
    protected function getMultiSelectOptions(string $column): array
    {
        // Common car features
        $commonFeatures = [
            'Air Conditioning',
            'Power Steering',
            'Power Windows',
            'Central Locking',
            'Music System',
            'Bluetooth',
            'USB Port',
            'Navigation System',
            'Sunroof',
            'Alloy Wheels',
            'Fog Lights',
            'Rear Camera',
            'Parking Sensors',
        ];

        $safetyFeatures = [
            'ABS',
            'EBD',
            'Airbags',
            'Traction Control',
            'Stability Control',
            'Hill Assist',
            'Tire Pressure Monitor',
            'ISOFIX',
            'Reverse Camera',
            'Parking Sensors',
        ];

        return match($column) {
            'features' => $commonFeatures,
            'safety_features' => $safetyFeatures,
            default => [],
        };
    }

    /**
     * Get field label (human-readable)
     */
    protected function getFieldLabel(string $column): string
    {
        $labels = [
            'title' => 'Title',
            'make_id' => 'Make',
            'model_id' => 'Model',
            'city_id' => 'City',
            'year' => 'Year',
            'price' => 'Price (₹)',
            'condition' => 'Condition',
            'mileage' => 'Mileage (km)',
            'vin' => 'VIN Number',
            'registration_number' => 'Registration Number',
            'fuel_type' => 'Fuel Type',
            'transmission' => 'Transmission',
            'engine_capacity' => 'Engine Capacity',
            'power' => 'Power (HP)',
            'torque' => 'Torque (Nm)',
            'mileage_kmpl' => 'Fuel Efficiency (kmpl)',
            'exterior_color' => 'Exterior Color',
            'interior_color' => 'Interior Color',
            'seats' => 'Number of Seats',
            'doors' => 'Number of Doors',
            'features' => 'Features',
            'safety_features' => 'Safety Features',
            'owners' => 'Number of Owners',
            'insurance_valid' => 'Insurance Valid',
            'insurance_expiry' => 'Insurance Expiry Date',
            'under_warranty' => 'Under Warranty',
            'service_history' => 'Service History',
            'description' => 'Description',
            'created_at' => 'Created Date',
        ];

        return $labels[$column] ?? ucwords(str_replace('_', ' ', $column));
    }

    /**
     * Check if field is required
     */
    protected function isRequired(string $column): bool
    {
        $requiredFields = [
            'title', 'make_id', 'model_id', 'city_id', 'year', 'price',
            'condition', 'fuel_type', 'transmission', 'exterior_color',
            'seats', 'doors', 'owners',
        ];

        return in_array($column, $requiredFields);
    }

    /**
     * Check if field is nullable
     */
    protected function isNullable(string $column): bool
    {
        try {
            $connection = DB::connection();
            $doctrineColumn = $connection->getDoctrineColumn($this->tableName, $column);
            return !$doctrineColumn->getNotnull();
        } catch (\Exception $e) {
            // Default to nullable if we can't determine
            return true;
        }
    }

    /**
     * Get min value for number inputs
     */
    protected function getMinValue(string $column): ?int
    {
        return match($column) {
            'year' => 1900,
            'mileage', 'power', 'torque', 'owners' => 0,
            'seats' => 2,
            'doors' => 2,
            'price', 'mileage_kmpl' => 0,
            default => null,
        };
    }

    /**
     * Get max value for number inputs
     */
    protected function getMaxValue(string $column): ?int
    {
        return match($column) {
            'year' => (int)date('Y') + 1,
            'owners' => 10,
            'seats' => 15,
            'doors' => 6,
            default => null,
        };
    }

    /**
     * Get step value for number inputs
     */
    protected function getStepValue(string $column): float|int
    {
        return in_array($column, ['price', 'mileage_kmpl']) ? 0.01 : 1;
    }

    /**
     * Get textarea rows
     */
    protected function getTextareaRows(string $column): int
    {
        return match($column) {
            'description' => 6,
            'service_history' => 4,
            default => 3,
        };
    }

    /**
     * Get display format for listing
     */
    protected function getDisplayFormat(string $column): string
    {
        return match($column) {
            'price' => 'currency',
            'created_at' => 'date',
            'make_id', 'model_id' => 'relationship',
            'status' => 'badge',
            default => 'text',
        };
    }
}

