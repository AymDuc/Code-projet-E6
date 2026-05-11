using Microsoft.Data.Sqlite;
using System.Data;

namespace InfotechSAVManager;

public static class Database
{
    private static readonly string DbPath = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "sav_manager.db");
    private static readonly string ConnectionString = $"Data Source={DbPath}";

    public static void Initialize()
    {
        using var connection = new SqliteConnection(ConnectionString);
        connection.Open();

        string sql = @"
        CREATE TABLE IF NOT EXISTS interventions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            numero_dossier TEXT,
            client TEXT NOT NULL,
            telephone TEXT,
            appareil TEXT NOT NULL,
            marque TEXT,
            probleme TEXT NOT NULL,
            diagnostic TEXT,
            statut TEXT NOT NULL,
            date_depot TEXT NOT NULL,
            date_restitution TEXT
        );";

        using var command = new SqliteCommand(sql, connection);
        command.ExecuteNonQuery();

        // Migration simple pour les anciennes bases V1.
        EnsureColumn(connection, "numero_dossier", "TEXT");
        EnsureColumn(connection, "date_restitution", "TEXT");
        BackfillNumeroDossier(connection);
    }

    private static void EnsureColumn(SqliteConnection connection, string columnName, string columnType)
    {
        using var check = new SqliteCommand("PRAGMA table_info(interventions);", connection);
        using var reader = check.ExecuteReader();
        while (reader.Read())
        {
            if (reader["name"]?.ToString() == columnName)
                return;
        }

        using var alter = new SqliteCommand($"ALTER TABLE interventions ADD COLUMN {columnName} {columnType};", connection);
        alter.ExecuteNonQuery();
    }

    private static void BackfillNumeroDossier(SqliteConnection connection)
    {
        string sql = @"
        UPDATE interventions
        SET numero_dossier = 'SAV-' || strftime('%Y', 'now') || '-' || printf('%03d', id)
        WHERE numero_dossier IS NULL OR numero_dossier = '';";

        using var command = new SqliteCommand(sql, connection);
        command.ExecuteNonQuery();
    }

    public static DataTable GetInterventions(string search = "", string statutFilter = "Tous")
    {
        using var connection = new SqliteConnection(ConnectionString);
        connection.Open();

        string sql = @"
        SELECT 
            id AS 'ID',
            numero_dossier AS 'Dossier',
            client AS 'Client',
            telephone AS 'Téléphone',
            appareil AS 'Appareil',
            marque AS 'Marque',
            probleme AS 'Problème',
            diagnostic AS 'Diagnostic',
            statut AS 'Statut',
            date_depot AS 'Date dépôt',
            date_restitution AS 'Date restitution'
        FROM interventions
        WHERE (@statutFilter = 'Tous' OR statut = @statutFilter)
          AND (
            client LIKE @search 
            OR numero_dossier LIKE @search
            OR appareil LIKE @search 
            OR marque LIKE @search 
            OR probleme LIKE @search 
            OR statut LIKE @search
          )
        ORDER BY id DESC;";

        using var command = new SqliteCommand(sql, connection);
        command.Parameters.AddWithValue("@search", $"%{search}%");
        command.Parameters.AddWithValue("@statutFilter", statutFilter);

        using var reader = command.ExecuteReader();
        var table = new DataTable();
        table.Load(reader);
        return table;
    }

    public static DataTable GetStatistics()
    {
        using var connection = new SqliteConnection(ConnectionString);
        connection.Open();

        string sql = @"
        SELECT statut AS 'Statut', COUNT(*) AS 'Total'
        FROM interventions
        GROUP BY statut
        ORDER BY statut;";

        using var command = new SqliteCommand(sql, connection);
        using var reader = command.ExecuteReader();
        var table = new DataTable();
        table.Load(reader);
        return table;
    }

    public static void AddIntervention(string client, string telephone, string appareil, string marque, string probleme, string diagnostic, string statut, string dateRestitution)
    {
        using var connection = new SqliteConnection(ConnectionString);
        connection.Open();

        string dateDepot = DateTime.Now.ToString("dd/MM/yyyy HH:mm");

        string sql = @"
        INSERT INTO interventions 
        (numero_dossier, client, telephone, appareil, marque, probleme, diagnostic, statut, date_depot, date_restitution)
        VALUES 
        ('', @client, @telephone, @appareil, @marque, @probleme, @diagnostic, @statut, @dateDepot, @dateRestitution);
        SELECT last_insert_rowid();";

        using var command = new SqliteCommand(sql, connection);
        command.Parameters.AddWithValue("@client", client);
        command.Parameters.AddWithValue("@telephone", telephone);
        command.Parameters.AddWithValue("@appareil", appareil);
        command.Parameters.AddWithValue("@marque", marque);
        command.Parameters.AddWithValue("@probleme", probleme);
        command.Parameters.AddWithValue("@diagnostic", diagnostic);
        command.Parameters.AddWithValue("@statut", statut);
        command.Parameters.AddWithValue("@dateDepot", dateDepot);
        command.Parameters.AddWithValue("@dateRestitution", dateRestitution);

        long id = (long)command.ExecuteScalar()!;
        string numeroDossier = $"SAV-{DateTime.Now:yyyy}-{id:000}";

        using var updateNumero = new SqliteCommand("UPDATE interventions SET numero_dossier = @numero WHERE id = @id;", connection);
        updateNumero.Parameters.AddWithValue("@numero", numeroDossier);
        updateNumero.Parameters.AddWithValue("@id", id);
        updateNumero.ExecuteNonQuery();
    }

    public static void UpdateIntervention(int id, string client, string telephone, string appareil, string marque, string probleme, string diagnostic, string statut, string dateRestitution)
    {
        using var connection = new SqliteConnection(ConnectionString);
        connection.Open();

        string sql = @"
        UPDATE interventions SET
            client = @client,
            telephone = @telephone,
            appareil = @appareil,
            marque = @marque,
            probleme = @probleme,
            diagnostic = @diagnostic,
            statut = @statut,
            date_restitution = @dateRestitution
        WHERE id = @id;";

        using var command = new SqliteCommand(sql, connection);
        command.Parameters.AddWithValue("@id", id);
        command.Parameters.AddWithValue("@client", client);
        command.Parameters.AddWithValue("@telephone", telephone);
        command.Parameters.AddWithValue("@appareil", appareil);
        command.Parameters.AddWithValue("@marque", marque);
        command.Parameters.AddWithValue("@probleme", probleme);
        command.Parameters.AddWithValue("@diagnostic", diagnostic);
        command.Parameters.AddWithValue("@statut", statut);
        command.Parameters.AddWithValue("@dateRestitution", dateRestitution);
        command.ExecuteNonQuery();
    }

    public static void DeleteIntervention(int id)
    {
        using var connection = new SqliteConnection(ConnectionString);
        connection.Open();

        string sql = "DELETE FROM interventions WHERE id = @id;";
        using var command = new SqliteCommand(sql, connection);
        command.Parameters.AddWithValue("@id", id);
        command.ExecuteNonQuery();
    }
}
