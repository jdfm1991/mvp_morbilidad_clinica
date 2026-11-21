import re

input_file = "geografia_venezuela.sql"
output_file = "geografia_limpia.sql"

def extraer_datos_seguro(content, table_name):
    # Busca el INSERT INTO sin importar si tiene comillas invertidas o no
    # y captura todo hasta el primer punto y coma (;)
    pattern_insert = rf'INSERT INTO\s+`?{table_name}`?\s*.*?VALUES\s*(.*?);'
    match = re.search(pattern_insert, content, re.IGNORECASE | re.DOTALL)
    
    if not match:
        print(f"❌ No se encontró el bloque INSERT para '{table_name}'")
        return []
    
    values_str = match.group(1)
    
    # Regex para capturar tuplas: (1, 2, 'Nombre con espacios')
    # Explicación: 
    # \(\s* -> Abre paréntesis y espacios
    # (\d+) -> Grupo 1: ID (números)
    # \s*,\s* -> Coma y espacios
    # (\d+) -> Grupo 2: ID padre (números)
    # \s*,\s* -> Coma y espacios
    # '([^']*)' -> Grupo 3: Texto entre comillas simples (captura todo lo que no sea ')
    # \s*\) -> Cierra paréntesis
    pattern_tupla = r'\(\s*(\d+)\s*,\s*(\d+)\s*,\s*\'([^\']*)\'\s*\)'
    tuplas = re.findall(pattern_tupla, values_str)
    
    return tuplas

# Leer el archivo original
with open(input_file, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

print("Buscando municipios...")
mun_tuplas = extraer_datos_seguro(content, "municipios")
print(f"✅ Encontrados {len(mun_tuplas)} municipios.")

print("Buscando parroquias...")
parr_tuplas = extraer_datos_seguro(content, "parroquias")
print(f"✅ Encontradas {len(parr_tuplas)} parroquias.")

# Generar el SQL para PostgreSQL
with open(output_file, 'w', encoding='utf-8') as f:
    f.write("-- Datos generados para PostgreSQL\n\n")
    
    # Insertar Municipios
    if mun_tuplas:
        f.write("INSERT INTO municipios (id, estado_id, nombre, created_at, updated_at) VALUES\n")
        valores = []
        for t in mun_tuplas:
            nombre_limpio = t[2].replace("'", "''") # Escapar comillas simples internas
            valores.append(f"({t[0]}, {t[1]}, '{nombre_limpio}', NOW(), NOW())")
        f.write(",\n".join(valores) + ";\n\n")

    # Insertar Parroquias
    if parr_tuplas:
        f.write("INSERT INTO parroquias (id, municipio_id, nombre, created_at, updated_at) VALUES\n")
        valores = []
        for t in parr_tuplas:
            nombre_limpio = t[2].replace("'", "''")
            valores.append(f"({t[0]}, {t[1]}, '{nombre_limpio}', NOW(), NOW())")
        f.write(",\n".join(valores) + ";\n\n")

    # Ajustar secuencias de PostgreSQL
    f.write("-- Ajuste de secuencias\n")
    if mun_tuplas:
        f.write("SELECT setval('municipios_id_seq', (SELECT MAX(id) FROM municipios));\n")
    if parr_tuplas:
        f.write("SELECT setval('parroquias_id_seq', (SELECT MAX(id) FROM parroquias));\n")

print(f"\n🎉 Archivo '{output_file}' generado exitosamente.")