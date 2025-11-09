export default function useExport() {
    function toArrayOfObjects(data: any): any[] {
        if (Array.isArray(data)) return data;
        if (data && typeof data === 'object' && Array.isArray(data.rows)) return data.rows;
        return [];
    }

    function inferHeaders(rows: any[]): string[] {
        if (!rows.length) return [];
        const keys = new Set<string>();
        for (const row of rows.slice(0, 10)) {
            Object.keys(row || {}).forEach((k) => keys.add(k));
        }
        return Array.from(keys);
    }

    async function parseJSONFile(file: File) {
        const text = await file.text();
        const json = JSON.parse(text);
        const rows = toArrayOfObjects(json);
        return {
            rows,
            headers: inferHeaders(rows),
        };
    }

    async function parseCSVFile(file: File) {
        const Papa = (await import('papaparse')).default;
        return new Promise<{ rows: any[]; headers: string[] }>((reject) => {
            Papa.parse(file, {
                header: true,
                skipEmptyLines: true,
                complete: (result: any) => {
                    const rows = result.data as any[];
                    return {
                        rows,
                        headers: inferHeaders(rows),
                    };
                },
                error: (err: any) => reject(err),
            });
        });
    }

    async function parseExcelFile(file: File) {
        const XLSX = await import('xlsx');
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheet = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheet];
        const json = XLSX.utils.sheet_to_json(worksheet, { defval: null });
        const rows = json as any[];

        return {
            rows,
            headers: inferHeaders(rows),
        };
    }

    function getExt(name: string): string {
        const i = name.lastIndexOf('.');
        return i >= 0 ? name.slice(i + 1).toLowerCase() : '';
    }

    return {
        parseCSVFile,
        parseJSONFile,
        parseExcelFile,
        getExt,
    };
}
