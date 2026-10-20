namespace Rabota3
{
    partial class Form1
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
            this.SpisokL = new System.Windows.Forms.Label();
            this.listBox1 = new System.Windows.Forms.ListBox();
            this.BtnDobavit = new System.Windows.Forms.Button();
            this.BtnIzmenit = new System.Windows.Forms.Button();
            this.BtnUdalit = new System.Windows.Forms.Button();
            this.SuspendLayout();
            //
            // SpisokL
            //
            this.SpisokL.AutoSize = true;
            this.SpisokL.Location = new System.Drawing.Point(12, 11);
            this.SpisokL.Name = "SpisokL";
            this.SpisokL.Size = new System.Drawing.Size(67, 13);
            this.SpisokL.TabIndex = 0;
            this.SpisokL.Text = "Список блюд";
            //
            // listBox1
            //
            this.listBox1.FormattingEnabled = true;
            this.listBox1.Location = new System.Drawing.Point(15, 28);
            this.listBox1.Name = "listBox1";
            this.listBox1.Size = new System.Drawing.Size(280, 173);
            this.listBox1.TabIndex = 1;
            //
            // BtnDobavit
            //
            this.BtnDobavit.Location = new System.Drawing.Point(305, 28);
            this.BtnDobavit.Name = "BtnDobavit";
            this.BtnDobavit.Size = new System.Drawing.Size(105, 30);
            this.BtnDobavit.TabIndex = 2;
            this.BtnDobavit.Text = "&Добавить";
            this.BtnDobavit.UseVisualStyleBackColor = true;
            this.BtnDobavit.Click += new System.EventHandler(this.BtnDobavit_Click);
            //
            // BtnIzmenit
            //
            this.BtnIzmenit.Location = new System.Drawing.Point(305, 64);
            this.BtnIzmenit.Name = "BtnIzmenit";
            this.BtnIzmenit.Size = new System.Drawing.Size(105, 30);
            this.BtnIzmenit.TabIndex = 3;
            this.BtnIzmenit.Text = "&Изменить";
            this.BtnIzmenit.UseVisualStyleBackColor = true;
            this.BtnIzmenit.Click += new System.EventHandler(this.BtnIzmenit_Click);
            //
            // BtnUdalit
            //
            this.BtnUdalit.Location = new System.Drawing.Point(305, 100);
            this.BtnUdalit.Name = "BtnUdalit";
            this.BtnUdalit.Size = new System.Drawing.Size(105, 30);
            this.BtnUdalit.TabIndex = 4;
            this.BtnUdalit.Text = "&Удалить";
            this.BtnUdalit.UseVisualStyleBackColor = true;
            this.BtnUdalit.Click += new System.EventHandler(this.BtnUdalit_Click);
            //
            // Form1
            //
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(425, 220);
            this.Controls.Add(this.BtnUdalit);
            this.Controls.Add(this.BtnIzmenit);
            this.Controls.Add(this.BtnDobavit);
            this.Controls.Add(this.listBox1);
            this.Controls.Add(this.SpisokL);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.MaximizeBox = false;
            this.Name = "Form1";
            this.Text = "Список блюд";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label SpisokL;
        private System.Windows.Forms.ListBox listBox1;
        private System.Windows.Forms.Button BtnDobavit;
        private System.Windows.Forms.Button BtnIzmenit;
        private System.Windows.Forms.Button BtnUdalit;
    }
}
