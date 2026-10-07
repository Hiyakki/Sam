<x-section bg="surface" id="statistik">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10">
            <x-stat
                :number="$stats['since_year']"
                label="Tahun Berdiri & Pengalaman"
            />
            <x-stat
                :number="$stats['branches_count']"
                suffix="Lokasi"
                label="Kantor Cabang Aktif"
            />
            <x-stat
                :number="$stats['products_count']"
                suffix="+"
                label="Produk & Komponen Katalog"
            />
            <x-stat
                :number="$stats['services_count']"
                label="Layanan Klinis & Fabrikasi"
            />
        </div>
    </div>
</x-section>
