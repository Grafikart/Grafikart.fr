<?php

namespace App\Providers;

use App\Infrastructure\TypeScript\AttributedDataClassTransformer;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Spatie\LaravelTypeScriptTransformer\LaravelData\LaravelDataTypeScriptTransformerExtension;
use Spatie\LaravelTypeScriptTransformer\LaravelData\Transformers\DataClassTransformer;
use Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider as BaseTypeScriptTransformerServiceProvider;
use Spatie\TypeScriptTransformer\Transformers\AttributedClassTransformer;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfigFactory;
use Spatie\TypeScriptTransformer\Writers\FlatModuleWriter;

class TypeScriptTransformerServiceProvider extends BaseTypeScriptTransformerServiceProvider
{
    protected function configure(TypeScriptTransformerConfigFactory $config): void
    {
        $config
            ->transformer(AttributedClassTransformer::class)
            ->transformer(EnumTransformer::class)
            ->transformDirectories(app_path())
            ->extension(new LaravelDataTypeScriptTransformerExtension)
            ->replaceTransformer(DataClassTransformer::class, AttributedDataClassTransformer::class)
            ->replaceType(\DateTime::class, 'string')
            ->replaceType(\DateTimeImmutable::class, 'string')
            ->replaceType(\DateTimeInterface::class, 'string')
            ->replaceType(CarbonInterface::class, 'string')
            ->replaceType(CarbonImmutable::class, 'string')
            ->replaceType(Carbon::class, 'string')
            ->outputDirectory(resource_path('js/types'))
            ->writer(new FlatModuleWriter('generated.d.ts'))
            ->withoutManifest();
    }
}
