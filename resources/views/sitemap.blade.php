{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>{{ route('home') }}</loc></url><url><loc>{{ route('creators') }}</loc></url>@foreach($creators as $creator)<url><loc>{{ route('creator.show',$creator->slug) }}</loc><lastmod>{{ $creator->updated_at->toAtomString() }}</lastmod></url>@endforeach</urlset>
