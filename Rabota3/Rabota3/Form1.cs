using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Rabota3
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private void BtnDobavit_Click(object sender, EventArgs e)
        {
            Form2 Okno = new Form2();
            Okno.Text = "Добавление человека";
            if (Okno.ShowDialog() == DialogResult.OK)
            {
                Person Chel = new Person();
                Chel.Surname = Okno.Surname_TB.Text.Trim();
                Chel.BirthYear = Convert.ToInt32(Okno.Year_TB.Text);
                Chel.Gender = Okno.Male_RB.Checked ? "М" : "Ж";
                Chel.Height = Convert.ToInt32(Okno.Height_TB.Text);
                listBox1.Items.Add(Chel);
            }
        }

        private void BtnIzmenit_Click(object sender, EventArgs e)
        {
            if (listBox1.SelectedIndex != -1)
            {
                Person Chel = listBox1.SelectedItem as Person;
                Form2 Okno = new Form2();
                Okno.Text = "Изменение данных";
                Okno.Surname_TB.Text = Chel.Surname;
                Okno.Year_TB.Text = Chel.BirthYear.ToString();
                if (Chel.Gender == "М")
                    Okno.Male_RB.Checked = true;
                else
                    Okno.Female_RB.Checked = true;
                Okno.Height_TB.Text = Chel.Height.ToString();
                if (Okno.ShowDialog() == DialogResult.OK)
                {
                    Chel.Surname = Okno.Surname_TB.Text.Trim();
                    Chel.BirthYear = Convert.ToInt32(Okno.Year_TB.Text);
                    Chel.Gender = Okno.Male_RB.Checked ? "М" : "Ж";
                    Chel.Height = Convert.ToInt32(Okno.Height_TB.Text);
                    listBox1.Items[listBox1.SelectedIndex] = Chel;
                }
            }
            else
                MessageBox.Show("Не выбран элемент в списке", "Информация");
        }

        private void BtnUdalit_Click(object sender, EventArgs e)
        {
            if (listBox1.SelectedIndex != -1)
                listBox1.Items.RemoveAt(listBox1.SelectedIndex);
            else
                MessageBox.Show("Не выбран элемент в списке", "Информация");
        }
    }
}
