@extends('layouts.app')

@section('title', 'Daftar User')
@section('page-title', 'Daftar User')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <h3 class="text-lg font-semibold text-gray-700">Semua User</h3>

        <a href="{{ route('users.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 text-center">
            + Tambah User
        </a>

    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm min-w-[700px]">

            <thead>
                <tr class="text-left text-gray-500 bg-gray-50 border-b">
                    <th class="px-6 py-3 whitespace-nowrap">Nama</th>
                    <th class="px-6 py-3 whitespace-nowrap">Email</th>
                    <th class="px-6 py-3 whitespace-nowrap">Role</th>
                    <th class="px-6 py-3 whitespace-nowrap">Status</th>
                    <th class="px-6 py-3 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                    <tr class="border-b last:border-0 hover:bg-gray-50">

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($user->isOnline())
                                <span class="inline-block w-2.5 h-2.5 bg-green-500 rounded-full mr-2" title="Online"></span>
                            @else
                                <span class="inline-block w-2.5 h-2.5 bg-gray-300 rounded-full mr-2" title="Offline"></span>
                            @endif

                            <span class="font-medium">{{ $user->name }}</span>
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $user->email }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                {{ $user->roles->first()->name ?? '-' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">

                            @if ($user->is_active)
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                    Nonaktif
                                </span>
                            @endif

                            <span class="ml-1 text-xs text-gray-400">
                                @if ($user->isOnline())
                                    <span class="text-green-700">(Online)</span>
                                @else
                                    (Offline)
                                @endif
                            </span>

                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:underline text-xs">
                                    Edit
                                </a>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            Belum ada user.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

@endsection
