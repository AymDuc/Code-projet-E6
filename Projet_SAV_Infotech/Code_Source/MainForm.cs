using System.Data;
using System.Text;

namespace InfotechSAVManager;

public class MainForm : Form
{
    private readonly DataGridView grid = new();
    private readonly TextBox txtSearch = new();
    private readonly ComboBox cbFilterStatut = new();
    private readonly TextBox txtClient = new();
    private readonly TextBox txtTelephone = new();
    private readonly ComboBox cbAppareil = new();
    private readonly TextBox txtMarque = new();
    private readonly TextBox txtProbleme = new();
    private readonly TextBox txtDiagnostic = new();
    private readonly ComboBox cbStatut = new();
    private readonly DateTimePicker dtRestitution = new();
    private readonly CheckBox chkRestitution = new();
    private readonly Button btnAdd = new();
    private readonly Button btnUpdate = new();
    private readonly Button btnDelete = new();
    private readonly Button btnClear = new();
    private readonly Button btnExportCsv = new();
    private readonly Label lblSelectedId = new();
    private readonly Label lblStats = new();

    private int? selectedId = null;

    public MainForm()
    {
        Text = Config.AppName;
        Width = 1250;
        Height = 760;
        StartPosition = FormStartPosition.CenterScreen;
        MinimumSize = new Size(1050, 680);

        BuildInterface();
        LoadData();
    }

    private void BuildInterface()
    {
        BackColor = Color.White;

        var mainPanel = new TableLayoutPanel
        {
            Dock = DockStyle.Fill,
            ColumnCount = 2,
            RowCount = 1,
            Padding = new Padding(12)
        };
        mainPanel.ColumnStyles.Add(new ColumnStyle(SizeType.Percent, 68));
        mainPanel.ColumnStyles.Add(new ColumnStyle(SizeType.Percent, 32));
        Controls.Add(mainPanel);

        var leftPanel = new TableLayoutPanel
        {
            Dock = DockStyle.Fill,
            RowCount = 4
        };
        leftPanel.RowStyles.Add(new RowStyle(SizeType.Absolute, 48));
        leftPanel.RowStyles.Add(new RowStyle(SizeType.Absolute, 42));
        leftPanel.RowStyles.Add(new RowStyle(SizeType.Percent, 100));
        leftPanel.RowStyles.Add(new RowStyle(SizeType.Absolute, 48));
        mainPanel.Controls.Add(leftPanel, 0, 0);

        var headerPanel = new FlowLayoutPanel { Dock = DockStyle.Fill };
        var title = new Label
        {
            Text = "Infotech SAV Manager",
            AutoSize = true,
            Font = new Font(FontFamily.GenericSansSerif, 18, FontStyle.Bold),
            ForeColor = Color.FromArgb(20, 45, 35),
            Padding = new Padding(0, 6, 20, 0)
        };
        headerPanel.Controls.Add(title);
        lblStats.AutoSize = true;
        lblStats.Padding = new Padding(0, 12, 0, 0);
        headerPanel.Controls.Add(lblStats);
        leftPanel.Controls.Add(headerPanel, 0, 0);

        var searchPanel = new FlowLayoutPanel { Dock = DockStyle.Fill };
        searchPanel.Controls.Add(new Label { Text = "Recherche :", AutoSize = true, Padding = new Padding(0, 8, 8, 0) });
        txtSearch.Width = 280;
        txtSearch.TextChanged += (s, e) => LoadData();
        searchPanel.Controls.Add(txtSearch);

        searchPanel.Controls.Add(new Label { Text = "Statut :", AutoSize = true, Padding = new Padding(18, 8, 8, 0) });
        cbFilterStatut.DropDownStyle = ComboBoxStyle.DropDownList;
        cbFilterStatut.Width = 180;
        cbFilterStatut.Items.Add("Tous");
        cbFilterStatut.Items.AddRange(Config.Statuts);
        cbFilterStatut.SelectedIndex = 0;
        cbFilterStatut.SelectedIndexChanged += (s, e) => LoadData();
        searchPanel.Controls.Add(cbFilterStatut);
        leftPanel.Controls.Add(searchPanel, 0, 1);

        grid.Dock = DockStyle.Fill;
        grid.ReadOnly = true;
        grid.SelectionMode = DataGridViewSelectionMode.FullRowSelect;
        grid.MultiSelect = false;
        grid.AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill;
        grid.AllowUserToAddRows = false;
        grid.CellClick += Grid_CellClick;
        leftPanel.Controls.Add(grid, 0, 2);

        var bottomPanel = new FlowLayoutPanel { Dock = DockStyle.Fill };
        btnExportCsv.Text = "Exporter CSV";
        btnExportCsv.Width = 140;
        btnExportCsv.Click += BtnExportCsv_Click;
        bottomPanel.Controls.Add(btnExportCsv);
        leftPanel.Controls.Add(bottomPanel, 0, 3);

        var formPanel = new TableLayoutPanel
        {
            Dock = DockStyle.Fill,
            RowCount = 22,
            Padding = new Padding(15),
            BackColor = Color.FromArgb(244, 248, 245)
        };
        mainPanel.Controls.Add(formPanel, 1, 0);

        var formTitle = new Label
        {
            Text = "Fiche intervention SAV",
            Font = new Font(FontFamily.GenericSansSerif, 15, FontStyle.Bold),
            AutoSize = true,
            Padding = new Padding(0, 0, 0, 12)
        };
        formPanel.Controls.Add(formTitle);

        AddLabelAndControl(formPanel, "Client *", txtClient);
        AddLabelAndControl(formPanel, "Téléphone", txtTelephone);

        cbAppareil.DropDownStyle = ComboBoxStyle.DropDownList;
        cbAppareil.Items.AddRange(Config.TypesAppareils);
        cbAppareil.SelectedIndex = 0;
        AddLabelAndControl(formPanel, "Appareil *", cbAppareil);

        AddLabelAndControl(formPanel, "Marque / modèle", txtMarque);

        txtProbleme.Multiline = true;
        txtProbleme.Height = 65;
        AddLabelAndControl(formPanel, "Problème constaté *", txtProbleme);

        txtDiagnostic.Multiline = true;
        txtDiagnostic.Height = 65;
        AddLabelAndControl(formPanel, "Diagnostic / action réalisée", txtDiagnostic);

        cbStatut.DropDownStyle = ComboBoxStyle.DropDownList;
        cbStatut.Items.AddRange(Config.Statuts);
        cbStatut.SelectedIndex = 0;
        AddLabelAndControl(formPanel, "Statut", cbStatut);

        chkRestitution.Text = "Date de restitution prévue";
        chkRestitution.AutoSize = true;
        chkRestitution.CheckedChanged += (s, e) => dtRestitution.Enabled = chkRestitution.Checked;
        formPanel.Controls.Add(chkRestitution);

        dtRestitution.Format = DateTimePickerFormat.Short;
        dtRestitution.Enabled = false;
        formPanel.Controls.Add(dtRestitution);

        lblSelectedId.Text = "Aucune intervention sélectionnée";
        lblSelectedId.AutoSize = true;
        lblSelectedId.Padding = new Padding(0, 8, 0, 8);
        formPanel.Controls.Add(lblSelectedId);

        var buttons = new FlowLayoutPanel { Dock = DockStyle.Fill, Height = 90 };

        btnAdd.Text = "Ajouter";
        btnAdd.Width = 100;
        btnAdd.Click += BtnAdd_Click;

        btnUpdate.Text = "Modifier";
        btnUpdate.Width = 100;
        btnUpdate.Click += BtnUpdate_Click;

        btnDelete.Text = "Supprimer";
        btnDelete.Width = 100;
        btnDelete.Click += BtnDelete_Click;

        btnClear.Text = "Vider";
        btnClear.Width = 100;
        btnClear.Click += (s, e) => ClearForm();

        buttons.Controls.Add(btnAdd);
        buttons.Controls.Add(btnUpdate);
        buttons.Controls.Add(btnDelete);
        buttons.Controls.Add(btnClear);

        formPanel.Controls.Add(buttons);
    }

    private void AddLabelAndControl(TableLayoutPanel panel, string label, Control control)
    {
        panel.Controls.Add(new Label
        {
            Text = label,
            AutoSize = true,
            Font = new Font(FontFamily.GenericSansSerif, 9, FontStyle.Bold),
            Padding = new Padding(0, 8, 0, 2)
        });

        control.Dock = DockStyle.Top;
        panel.Controls.Add(control);
    }

    private void LoadData()
    {
        string selectedFilter = cbFilterStatut.SelectedItem?.ToString() ?? "Tous";
        grid.DataSource = Database.GetInterventions(txtSearch.Text.Trim(), selectedFilter);
        UpdateStats();

        if (grid.Columns.Contains("ID"))
            grid.Columns["ID"].Width = 45;
        if (grid.Columns.Contains("Dossier"))
            grid.Columns["Dossier"].Width = 95;
    }

    private void UpdateStats()
    {
        DataTable stats = Database.GetStatistics();
        int total = 0;
        var fragments = new List<string>();

        foreach (DataRow row in stats.Rows)
        {
            string statut = row["Statut"].ToString() ?? "";
            int count = Convert.ToInt32(row["Total"]);
            total += count;
            fragments.Add($"{statut}: {count}");
        }

        lblStats.Text = total == 0 ? "Aucune intervention" : $"Total: {total} | " + string.Join(" | ", fragments);
    }

    private bool ValidateForm()
    {
        if (string.IsNullOrWhiteSpace(txtClient.Text))
        {
            MessageBox.Show("Le nom du client est obligatoire.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return false;
        }

        if (string.IsNullOrWhiteSpace(cbAppareil.Text))
        {
            MessageBox.Show("Le type d'appareil est obligatoire.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return false;
        }

        if (string.IsNullOrWhiteSpace(txtProbleme.Text))
        {
            MessageBox.Show("Le problème constaté est obligatoire.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return false;
        }

        return true;
    }

    private string GetDateRestitution()
    {
        return chkRestitution.Checked ? dtRestitution.Value.ToString("dd/MM/yyyy") : "";
    }

    private void BtnAdd_Click(object? sender, EventArgs e)
    {
        if (!ValidateForm()) return;

        Database.AddIntervention(
            txtClient.Text.Trim(),
            txtTelephone.Text.Trim(),
            cbAppareil.Text.Trim(),
            txtMarque.Text.Trim(),
            txtProbleme.Text.Trim(),
            txtDiagnostic.Text.Trim(),
            cbStatut.Text.Trim(),
            GetDateRestitution()
        );

        LoadData();
        ClearForm();
        MessageBox.Show("Intervention ajoutée avec succès.", "SAV", MessageBoxButtons.OK, MessageBoxIcon.Information);
    }

    private void BtnUpdate_Click(object? sender, EventArgs e)
    {
        if (selectedId == null)
        {
            MessageBox.Show("Sélectionnez une intervention à modifier.", "SAV", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return;
        }

        if (!ValidateForm()) return;

        Database.UpdateIntervention(
            selectedId.Value,
            txtClient.Text.Trim(),
            txtTelephone.Text.Trim(),
            cbAppareil.Text.Trim(),
            txtMarque.Text.Trim(),
            txtProbleme.Text.Trim(),
            txtDiagnostic.Text.Trim(),
            cbStatut.Text.Trim(),
            GetDateRestitution()
        );

        LoadData();
        ClearForm();
        MessageBox.Show("Intervention modifiée avec succès.", "SAV", MessageBoxButtons.OK, MessageBoxIcon.Information);
    }

    private void BtnDelete_Click(object? sender, EventArgs e)
    {
        if (selectedId == null)
        {
            MessageBox.Show("Sélectionnez une intervention à supprimer.", "SAV", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return;
        }

        var confirm = MessageBox.Show("Voulez-vous vraiment supprimer cette intervention ?", "Confirmation", MessageBoxButtons.YesNo, MessageBoxIcon.Question);

        if (confirm == DialogResult.Yes)
        {
            Database.DeleteIntervention(selectedId.Value);
            LoadData();
            ClearForm();
        }
    }

    private void Grid_CellClick(object? sender, DataGridViewCellEventArgs e)
    {
        if (e.RowIndex < 0) return;

        var row = grid.Rows[e.RowIndex];
        selectedId = Convert.ToInt32(row.Cells["ID"].Value);

        txtClient.Text = row.Cells["Client"].Value?.ToString();
        txtTelephone.Text = row.Cells["Téléphone"].Value?.ToString();
        cbAppareil.Text = row.Cells["Appareil"].Value?.ToString();
        txtMarque.Text = row.Cells["Marque"].Value?.ToString();
        txtProbleme.Text = row.Cells["Problème"].Value?.ToString();
        txtDiagnostic.Text = row.Cells["Diagnostic"].Value?.ToString();
        cbStatut.Text = row.Cells["Statut"].Value?.ToString();

        string? dateRestitution = row.Cells["Date restitution"].Value?.ToString();
        chkRestitution.Checked = !string.IsNullOrWhiteSpace(dateRestitution);
        if (chkRestitution.Checked && DateTime.TryParse(dateRestitution, out DateTime parsedDate))
            dtRestitution.Value = parsedDate;

        lblSelectedId.Text = $"Intervention sélectionnée : {row.Cells["Dossier"].Value}";
    }

    private void ClearForm()
    {
        selectedId = null;
        txtClient.Clear();
        txtTelephone.Clear();
        cbAppareil.SelectedIndex = 0;
        txtMarque.Clear();
        txtProbleme.Clear();
        txtDiagnostic.Clear();
        cbStatut.SelectedIndex = 0;
        chkRestitution.Checked = false;
        dtRestitution.Value = DateTime.Today;
        lblSelectedId.Text = "Aucune intervention sélectionnée";
    }

    private void BtnExportCsv_Click(object? sender, EventArgs e)
    {
        if (grid.Rows.Count == 0)
        {
            MessageBox.Show("Aucune donnée à exporter.", "Export", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return;
        }

        using var saveDialog = new SaveFileDialog
        {
            Filter = "Fichier CSV (*.csv)|*.csv",
            FileName = $"export_sav_{DateTime.Now:yyyyMMdd_HHmm}.csv"
        };

        if (saveDialog.ShowDialog() != DialogResult.OK)
            return;

        var sb = new StringBuilder();

        for (int i = 0; i < grid.Columns.Count; i++)
        {
            sb.Append(grid.Columns[i].HeaderText);
            if (i < grid.Columns.Count - 1) sb.Append(";");
        }
        sb.AppendLine();

        foreach (DataGridViewRow row in grid.Rows)
        {
            if (row.IsNewRow) continue;

            for (int i = 0; i < grid.Columns.Count; i++)
            {
                string value = row.Cells[i].Value?.ToString()?.Replace(";", ",") ?? "";
                sb.Append(value);
                if (i < grid.Columns.Count - 1) sb.Append(";");
            }
            sb.AppendLine();
        }

        File.WriteAllText(saveDialog.FileName, sb.ToString(), Encoding.UTF8);
        MessageBox.Show("Export CSV terminé.", "Export", MessageBoxButtons.OK, MessageBoxIcon.Information);
    }
}
