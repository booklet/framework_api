# Paperclip

## Instalacja

### Config:

- domeny która będzie używana przy generowaniu pełnych urli
`Config::set('paperclip_host', 'http://booklet.pl');`

- jeśli ścieżka do narzędzia `identify` jest inna niż domyślna to należy ja podać:
`Config::set('custom_identify_bin_path', '/usr/local/bin/identify');`

### Model

W modelu w którym chcemy użyć dodajemy trait

`use NewPaperClipTrait;`

dodaje on do modelu dynamiczne metody takie jak (przykład dla preview):

`$this->preview()`
Zwraca tablicę danych o załączniku:
`preview_file_name`, `preview_file_size`, `preview_content_type`, `preview_updated_at`

`$this->previewPath(), $this->previewPath('style_name')`
Zwraca ścieżkę do pliku (parametr opcjonalny to nazwa stylu dla którego chcemy ścieżkę, domyślnie original)

`$this->previewUrl(), $this->previewUrl('style_name')`
Zwraca pełny URL do pliku (parametr opcjonalny to nazwa stylu dla którego chcemy ścieżkę, domyślnie original)

`$this->previewSave(array $file) or (string $file_path) without validation!`
Metoda wykorzystywana przy zapisywaniu, nie korzystać z niej, używać save() na modelu

`$this->previewReprocess()`
Przetworzenie plików (np. po zmianie wielkości styli)

`$this->previewDestroy()`
Skasowanie załącznika, docelowo będzie podpięte pod $model->destroy();

oraz

`const ALLOWED_CONTENT_TYPE = [
    'application/pdf' => ['pdf'],
    'application/postscript' => ['ai', 'eps', 'ps'],
    'image/svg+xml' => ['svg', 'svgz'],
    'image/gif' => ['gif'],
    'image/jpeg' => ['jpeg', 'jpg', 'jpe'],
    'image/png' => ['png'],
    'image/tiff' => ['tiff', 'tif'],
    'image/bmp' => ['bmp'],
];

public function hasAttachedFile()
{
    return [
        'file' => [
            'styles' => [
                'thumbnail' => '60x60>',
            ],
            'content_type' => self::ALLOWED_CONTENT_TYPE,
            'max_size' => 10485760, // 10 MB
        ],
    ];
}`

`60x60` - skaluje proporcjonalnie do zadanego wymiaru (żaden z boków nie będzie dłuższy niż 60px)
`60x60>` - to samo co powyżej, ale tylko jeśli grafika jest większa, mniejsze nie zostaną powiększone
`60x60#` - kropowanie grafiki centralnie

oraz dodajemy specjalny atrybut o nazwie załączniki (attachment)

`public function specialPropertis()
{
    return ['file'];
}`

## Użycie

`$model_item = new ExampleModel();
$model_item->file = $file;
$model_item->save();
`

Jako zmienną $file możemy przekazać:
- tablice `$_FILES` (jeśli tabela zawiera więcej niż jeden plik, zostanie użyty tylko pierwszy)
- tablice files w postaci znormalizowanej, (jeśli tabela zawiera więcej niż jeden plik, zostanie
użyty tylko pierwszy) przykład:

  `$normalize_files = [
      [
          'name' => 'test1.jpg',
          'type' => 'image/jpeg',
          'tmp_name' => '/tmp/nsl54Gs',
          'error' => 0,
          'size' => 1715,
      ],
      [
          'name' => 'test2.jpg',
          'type' => 'image/jpeg',
          'tmp_name' => '/tmp/1sl54GC',
          'error' => 0,
          'size' => 5368,
      ],
  ];
  `
- ścieżkę do pliku
