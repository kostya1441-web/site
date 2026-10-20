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
            this.label1 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.label3 = new System.Windows.Forms.Label();
            this.label4 = new System.Windows.Forms.Label();
            this.Surname_TB = new System.Windows.Forms.TextBox();
            this.Year_TB = new System.Windows.Forms.TextBox();
            this.Male_RB = new System.Windows.Forms.RadioButton();
            this.Female_RB = new System.Windows.Forms.RadioButton();
            this.Height_TB = new System.Windows.Forms.TextBox();
            this.Ok_B = new System.Windows.Forms.Button();
            this.Cancel_B = new System.Windows.Forms.Button();
            this.SuspendLayout();
            //
            // label1
            //
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(13, 16);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(53, 13);
            this.label1.TabIndex = 0;
            this.label1.Text = "Фамилия";
            //
            // label2
            //
            this.label2.AutoSize = true;
            this.label2.Location = new System.Drawing.Point(13, 45);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(85, 13);
            this.label2.TabIndex = 1;
            this.label2.Text = "Год рождения";
            //
            // label3
            //
            this.label3.AutoSize = true;
            this.label3.Location = new System.Drawing.Point(13, 74);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(24, 13);
            this.label3.TabIndex = 2;
            this.label3.Text = "Пол";
            //
            // label4
            //
            this.label4.AutoSize = true;
            this.label4.Location = new System.Drawing.Point(13, 103);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(49, 13);
            this.label4.TabIndex = 3;
            this.label4.Text = "Рост, см";
            //
            // Surname_TB
            //
            this.Surname_TB.Location = new System.Drawing.Point(120, 13);
            this.Surname_TB.Name = "Surname_TB";
            this.Surname_TB.Size = new System.Drawing.Size(150, 20);
            this.Surname_TB.TabIndex = 4;
            //
            // Year_TB
            //
            this.Year_TB.Location = new System.Drawing.Point(120, 42);
            this.Year_TB.Name = "Year_TB";
            this.Year_TB.Size = new System.Drawing.Size(150, 20);
            this.Year_TB.TabIndex = 5;
            //
            // Male_RB
            //
            this.Male_RB.AutoSize = true;
            this.Male_RB.Location = new System.Drawing.Point(120, 73);
            this.Male_RB.Name = "Male_RB";
            this.Male_RB.Size = new System.Drawing.Size(47, 17);
            this.Male_RB.TabIndex = 6;
            this.Male_RB.TabStop = true;
            this.Male_RB.Text = "Муж.";
            this.Male_RB.UseVisualStyleBackColor = true;
            //
            // Female_RB
            //
            this.Female_RB.AutoSize = true;
            this.Female_RB.Location = new System.Drawing.Point(195, 73);
            this.Female_RB.Name = "Female_RB";
            this.Female_RB.Size = new System.Drawing.Size(45, 17);
            this.Female_RB.TabIndex = 7;
            this.Female_RB.TabStop = true;
            this.Female_RB.Text = "Жен.";
            this.Female_RB.UseVisualStyleBackColor = true;
            //
            // Height_TB
            //
            this.Height_TB.Location = new System.Drawing.Point(120, 100);
            this.Height_TB.Name = "Height_TB";
            this.Height_TB.Size = new System.Drawing.Size(150, 20);
            this.Height_TB.TabIndex = 8;
            //
            // Ok_B
            //
            this.Ok_B.DialogResult = System.Windows.Forms.DialogResult.OK;
            this.Ok_B.Location = new System.Drawing.Point(66, 140);
            this.Ok_B.Name = "Ok_B";
            this.Ok_B.Size = new System.Drawing.Size(75, 28);
            this.Ok_B.TabIndex = 9;
            this.Ok_B.Text = "&Ok";
            this.Ok_B.UseVisualStyleBackColor = true;
            //
            // Cancel_B
            //
            this.Cancel_B.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.Cancel_B.Location = new System.Drawing.Point(155, 140);
            this.Cancel_B.Name = "Cancel_B";
            this.Cancel_B.Size = new System.Drawing.Size(90, 28);
            this.Cancel_B.TabIndex = 10;
            this.Cancel_B.Text = "&Отмена";
            this.Cancel_B.UseVisualStyleBackColor = true;
            //
            // Form2
            //
            this.AcceptButton = this.Ok_B;
            this.CancelButton = this.Cancel_B;
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(293, 185);
            this.Controls.Add(this.Cancel_B);
            this.Controls.Add(this.Ok_B);
            this.Controls.Add(this.Height_TB);
            this.Controls.Add(this.Female_RB);
            this.Controls.Add(this.Male_RB);
            this.Controls.Add(this.Year_TB);
            this.Controls.Add(this.Surname_TB);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.label1);
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

        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Button Ok_B;
        private System.Windows.Forms.Button Cancel_B;
        public System.Windows.Forms.TextBox Surname_TB;
        public System.Windows.Forms.TextBox Year_TB;
        public System.Windows.Forms.RadioButton Male_RB;
        public System.Windows.Forms.RadioButton Female_RB;
        public System.Windows.Forms.TextBox Height_TB;
    }
}
