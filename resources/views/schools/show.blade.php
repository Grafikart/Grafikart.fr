@extends('users.user-layout')

@section('title', 'Ecoles')

@section('content')

    <div class="grid gap-10 grid-cols-1 lg:grid-cols-[1fr_300px]">
        <main>
            @if($coupons->isNotEmpty())
            <section class="flex flex-col gap-4">
                <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                    <x-lucide-clipboard-clock class="size-6 text-primary"/>
                    Inscriptions en attente
                </h2>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Coupon</th>
                        <th>Date</th>
                        <th>Email</th>
                        <th class="text-end">Mois</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($coupons as $coupon)
                        <tr>
                            <td>{{ $coupon->id }}</td>
                            <td>{{ $coupon->created_at->translatedFormat('j F Y') }}</td>
                            <td>{{ $coupon->email }}</td>
                            <td class="text-end">{{ $coupon->months }} mois</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            @if($students->isNotEmpty())
            <section class="flex flex-col gap-4 mt-8">
                <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                    <x-lucide-graduation-cap class="size-6 text-primary"/>
                    Étudiants
                </h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Email</th>
                        <th>Inscription</th>
                        <th>Fin abonnement</th>
                        <th class="text-end">Cours complétés</th>
                    </tr>
                    </thead>
                    <tbody>
                @foreach($students as $student)
                    <tr>
                        <td><a href="{{ route('schools.student', ['student' => $student->id]) }}">{{ $student->email }}</a></td>
                        <td>{{ $student->createdAt->translatedFormat('j F Y') }}</td>
                        <td>{{ $student->endAt }}</td>
                        <td class="text-end">{{ $student->completions }}</td>
                    </tr>
                @endforeach
                    </tbody>
                </table>
                {{ $students->links() }}
            </section>
            @endif

        </main>

        <aside class="space-y-8">

            <section>
                <h2 class="text-xl font-bold text-foreground-title mb-2">Importer des étudiants</h2>
                @if($school->credits)
                    <p>Il vous reste <strong>{{ $school->credits }} mois premium</strong> à donner à vos étudiants</p>
                @else
                    <p>
                        Vous avez utilisé tous vos <strong>mois premium</strong>. Veuillez <a href="{{ route('contact') }}">me contacter</a> pour importer de nouveaux étudiants.
                    </p>
                @endif

                <student-importer
                    action="{{ route('schools.import') }}"
                    example-url="{{ asset('schools/students.csv') }}"
                    credits="{{ $school->credits }}"
                    subject='@json($school->email_subject)'
                    message='@json($school->email_message)'
                ></student-importer>

            </section>

        </aside>
    </div>
@endsection
