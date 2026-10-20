namespace Rabota3
{
    partial class Form2
    {
        /// <summary>
        /// Обязательная переменная конструктора.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Освободить все используемые ресурсы.
        /// </summary>
        /// <param name="disposing">истинно, если управляемый ресурс должен быть удален; иначе ложно.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Код, автоматически созданный конструктором форм Windows

        /// <summary>
        /// Требуемый метод для поддержки конструктора — не изменяйте
        /// содержимое этого метода с помощью редактора кода.
        /// </summary>
        private void InitializeComponent()
        {
            this.lblNazv = new System.Windows.Forms.Label();
            this.lblVes = new System.Windows.Forms.Label();
            this.lblSeb = new System.Windows.Forms.Label();
            this.lblCena = new System.Windows.Forms.Label();
            this.Nazv_TB = new System.Windows.Forms.TextBox();
            this.Ves_TB = new System.Windows.Forms.TextBox();
            this.Seb_TB = new System.Windows.Forms.TextBox();
            this.Cena_TB = new System.Windows.Forms.TextBox();
            this.BtnOk = new System.Windows.Forms.Button();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.SuspendLayout();
            //
            // lblNazv
            //
            this.lblNazv.AutoSize = true;
            this.lblNazv.Location = new System.Drawing.Point(14, 18);
            this.lblNazv.Name = "lblNazv";
            this.lblNazv.Size = new System.Drawing.Size(63, 13);
            this.lblNazv.TabIndex = 0;
            this.lblNazv.Text = "Название";
            //
            // lblVes
            //
            this.lblVes.AutoSize = true;
            this.lblVes.Location = new System.Drawing.Point(14, 49);
            this.lblVes.Name = "lblVes";
            this.lblVes.Size = new System.Drawing.Size(49, 13);
            this.lblVes.TabIndex = 1;
            this.lblVes.Text = "Вес, кг";
            //
            // lblSeb
            //
            this.lblSeb.AutoSize = true;
            this.lblSeb.Location = new System.Drawing.Point(14, 80);
            this.lblSeb.Name = "lblSeb";
            this.lblSeb.Size = new System.Drawing.Size(108, 13);
            this.lblSeb.TabIndex = 2;
            this.lblSeb.Text = "Себестоимость, руб.";
            //
            // lblCena
            //
            this.lblCena.AutoSize = true;
            this.lblCena.Location = new System.Drawing.Point(14, 111);
            this.lblCena.Name = "lblCena";
            this.lblCena.Size = new System.Drawing.Size(66, 13);
            this.lblCena.TabIndex = 3;
            this.lblCena.Text = "Цена, руб.";
            //
            // Nazv_TB
            //
            this.Nazv_TB.Location = new System.Drawing.Point(150, 15);
            this.Nazv_TB.Name = "Nazv_TB";
            this.Nazv_TB.Size = new System.Drawing.Size(160, 20);
            this.Nazv_TB.TabIndex = 4;
            //
            // Ves_TB
            //
            this.Ves_TB.Location = new System.Drawing.Point(150, 46);
            this.Ves_TB.Name = "Ves_TB";
            this.Ves_TB.Size = new System.Drawing.Size(160, 20);
            this.Ves_TB.TabIndex = 5;
            //
            // Seb_TB
            //
            this.Seb_TB.Location = new System.Drawing.Point(150, 77);
            this.Seb_TB.Name = "Seb_TB";
            this.Seb_TB.Size = new System.Drawing.Size(160, 20);
            this.Seb_TB.TabIndex = 6;
            //
            // Cena_TB
            //
            this.Cena_TB.Location = new System.Drawing.Point(150, 108);
            this.Cena_TB.Name = "Cena_TB";
            this.Cena_TB.Size = new System.Drawing.Size(160, 20);
            this.Cena_TB.TabIndex = 7;
            //
            // BtnOk
            //
            this.BtnOk.DialogResult = System.Windows.Forms.DialogResult.OK;
            this.BtnOk.Location = new System.Drawing.Point(93, 150);
            this.BtnOk.Name = "BtnOk";
            this.BtnOk.Size = new System.Drawing.Size(80, 28);
            this.BtnOk.TabIndex = 8;
            this.BtnOk.Text = "&Ok";
            this.BtnOk.UseVisualStyleBackColor = true;
            //
            // BtnCancel
            //
            this.BtnCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.BtnCancel.Location = new System.Drawing.Point(187, 150);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(95, 28);
            this.BtnCancel.TabIndex = 9;
            this.BtnCancel.Text = "&Отмена";
            this.BtnCancel.UseVisualStyleBackColor = true;
            //
            // Form2
            //
            this.AcceptButton = this.BtnOk;
            this.CancelButton = this.BtnCancel;
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(330, 196);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.BtnOk);
            this.Controls.Add(this.Cena_TB);
            this.Controls.Add(this.Seb_TB);
            this.Controls.Add(this.Ves_TB);
            this.Controls.Add(this.Nazv_TB);
            this.Controls.Add(this.lblCena);
            this.Controls.Add(this.lblSeb);
            this.Controls.Add(this.lblVes);
            this.Controls.Add(this.lblNazv);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "Form2";
            this.ShowInTaskbar = false;
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent;
            this.Text = "Form2";
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.Form2_FormClosing);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label lblNazv;
        private System.Windows.Forms.Label lblVes;
        private System.Windows.Forms.Label lblSeb;
        private System.Windows.Forms.Label lblCena;
        private System.Windows.Forms.Button BtnOk;
        private System.Windows.Forms.Button BtnCancel;
        public System.Windows.Forms.TextBox Nazv_TB;
        public System.Windows.Forms.TextBox Ves_TB;
        public System.Windows.Forms.TextBox Seb_TB;
        public System.Windows.Forms.TextBox Cena_TB;
    }
}
