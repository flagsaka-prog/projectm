@extends('layouts.app')

@section('title', 'Daftar Resource')
@section('page-title', 'Daftar Resource')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <h3 class="text-lg font-semibold text-gray-700">Semua Resource</h3>

        <a href="{{ route('resources.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 text-center">
            + Tambah Resource
        </a>

    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm min-w-[900px]">

            <thead>
                <tr class="text-left text-gray-500 bg-gray-50 border-b">
                    <th class="px-6 py-3 whitespace-nowrap">Nama</th>
                    <th class="px-6 py-3 whitespace-nowrap">Tipe</th>
                    <th class="px-6 py-3 whitespace-nowrap">Inisial</th>
                    <th class="px-6 py-3 whitespace-nowrap">Group</th>
                    <th class="px-6 py-3 whitespace-nowrap">Max Units</th>
                    <th class="px-6 py-3 whitespace-nowrap">Std Rate</th>
                    <th class="px-6 py-3 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($resources as $resource)
                    <tr class="border-b last:border-0 hover:bg-gray-50">

                        <td class="px-6 py-4 font-medium whitespace-nowrap">
                            {{ $resource->name }}
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ ucfirst($resource->type) }}
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $resource->initials ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $resource->group ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $resource->max_units ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $resource->std_rate ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="class=px-6 py-3 space-x-2">

                                <a href="{{ route('resources.edit', $resource) }}"
                                    class="text-yellow-600 hover:underline text-xs">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('resources.destroy', $resource) }}" class="inline"
                                    onsubmit="return confirm('Hapus resource ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline text-xs">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            Belum ada resource.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4">
        {{ $resources->links() }}
    </div>

@endsection
