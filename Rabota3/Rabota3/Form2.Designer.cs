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
            this.lblSurname = new System.Windows.Forms.Label();
            this.lblYear = new System.Windows.Forms.Label();
            this.lblGender = new System.Windows.Forms.Label();
            this.lblHeight = new System.Windows.Forms.Label();
            this.Surname_TB = new System.Windows.Forms.TextBox();
            this.Year_TB = new System.Windows.Forms.TextBox();
            this.Male_RB = new System.Windows.Forms.RadioButton();
            this.Female_RB = new System.Windows.Forms.RadioButton();
            this.Height_TB = new System.Windows.Forms.TextBox();
            this.BtnOk = new System.Windows.Forms.Button();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.SuspendLayout();
            //
            // lblSurname
            //
            this.lblSurname.AutoSize = true;
            this.lblSurname.Location = new System.Drawing.Point(14, 18);
            this.lblSurname.Name = "lblSurname";
            this.lblSurname.Size = new System.Drawing.Size(53, 13);
            this.lblSurname.TabIndex = 0;
            this.lblSurname.Text = "Фамилия";
            //
            // lblYear
            //
            this.lblYear.AutoSize = true;
            this.lblYear.Location = new System.Drawing.Point(14, 49);
            this.lblYear.Name = "lblYear";
            this.lblYear.Size = new System.Drawing.Size(85, 13);
            this.lblYear.TabIndex = 1;
            this.lblYear.Text = "Год рождения";
            //
            // lblGender
            //
            this.lblGender.AutoSize = true;
            this.lblGender.Location = new System.Drawing.Point(14, 80);
            this.lblGender.Name = "lblGender";
            this.lblGender.Size = new System.Drawing.Size(24, 13);
            this.lblGender.TabIndex = 2;
            this.lblGender.Text = "Пол";
            //
            // lblHeight
            //
            this.lblHeight.AutoSize = true;
            this.lblHeight.Location = new System.Drawing.Point(14, 111);
            this.lblHeight.Name = "lblHeight";
            this.lblHeight.Size = new System.Drawing.Size(49, 13);
            this.lblHeight.TabIndex = 3;
            this.lblHeight.Text = "Рост, см";
            //
            // Surname_TB
            //
            this.Surname_TB.Location = new System.Drawing.Point(150, 15);
            this.Surname_TB.Name = "Surname_TB";
            this.Surname_TB.Size = new System.Drawing.Size(160, 20);
            this.Surname_TB.TabIndex = 4;
            //
            // Year_TB
            //
            this.Year_TB.Location = new System.Drawing.Point(150, 46);
            this.Year_TB.Name = "Year_TB";
            this.Year_TB.Size = new System.Drawing.Size(160, 20);
            this.Year_TB.TabIndex = 5;
            //
            // Male_RB
            //
            this.Male_RB.AutoSize = true;
            this.Male_RB.Location = new System.Drawing.Point(150, 79);
            this.Male_RB.Name = "Male_RB";
            this.Male_RB.Size = new System.Drawing.Size(51, 17);
            this.Male_RB.TabIndex = 6;
            this.Male_RB.TabStop = true;
            this.Male_RB.Text = "Муж.";
            this.Male_RB.UseVisualStyleBackColor = true;
            //
            // Female_RB
            //
            this.Female_RB.AutoSize = true;
            this.Female_RB.Location = new System.Drawing.Point(230, 79);
            this.Female_RB.Name = "Female_RB";
            this.Female_RB.Size = new System.Drawing.Size(49, 17);
            this.Female_RB.TabIndex = 7;
            this.Female_RB.TabStop = true;
            this.Female_RB.Text = "Жен.";
            this.Female_RB.UseVisualStyleBackColor = true;
            //
            // Height_TB
            //
            this.Height_TB.Location = new System.Drawing.Point(150, 108);
            this.Height_TB.Name = "Height_TB";
            this.Height_TB.Size = new System.Drawing.Size(160, 20);
            this.Height_TB.TabIndex = 8;
            //
            // BtnOk
            //
            this.BtnOk.DialogResult = System.Windows.Forms.DialogResult.OK;
            this.BtnOk.Location = new System.Drawing.Point(93, 150);
            this.BtnOk.Name = "BtnOk";
            this.BtnOk.Size = new System.Drawing.Size(80, 28);
            this.BtnOk.TabIndex = 9;
            this.BtnOk.Text = "&Ok";
            this.BtnOk.UseVisualStyleBackColor = true;
            //
            // BtnCancel
            //
            this.BtnCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.BtnCancel.Location = new System.Drawing.Point(187, 150);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(95, 28);
            this.BtnCancel.TabIndex = 10;
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
            this.Controls.Add(this.Height_TB);
            this.Controls.Add(this.Female_RB);
            this.Controls.Add(this.Male_RB);
            this.Controls.Add(this.Year_TB);
            this.Controls.Add(this.Surname_TB);
            this.Controls.Add(this.lblHeight);
            this.Controls.Add(this.lblGender);
            this.Controls.Add(this.lblYear);
            this.Controls.Add(this.lblSurname);
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

        private System.Windows.Forms.Label lblSurname;
        private System.Windows.Forms.Label lblYear;
        private System.Windows.Forms.Label lblGender;
        private System.Windows.Forms.Label lblHeight;
        private System.Windows.Forms.Button BtnOk;
        private System.Windows.Forms.Button BtnCancel;
        public System.Windows.Forms.TextBox Surname_TB;
        public System.Windows.Forms.TextBox Year_TB;
        public System.Windows.Forms.RadioButton Male_RB;
        public System.Windows.Forms.RadioButton Female_RB;
        public System.Windows.Forms.TextBox Height_TB;
    }
}
