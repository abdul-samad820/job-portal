<?php

namespace Tests\Concerns;

use Illuminate\Http\UploadedFile;

trait CreatesFakeUploads
{
    /**
     * A real PDF has to start with "%PDF-" for finfo/mime_content_type
     * to detect it as application/pdf. UploadedFile::fake()->create()
     * only fills the file with garbage bytes of the right *size* — it
     * does NOT produce valid PDF structure. Since Phase5 SEC-20 added
     * server-side MIME verification on the actual stored file (not just
     * the client-declared type), tests must use genuinely PDF-shaped
     * bytes or every resume upload gets silently rejected.
     */
    protected function fakePdf(string $name = 'resume.pdf'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($path, "%PDF-1.4\n%âãÏÓ\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF");

        return new UploadedFile($path, $name, 'application/pdf', null, true);
    }
}
