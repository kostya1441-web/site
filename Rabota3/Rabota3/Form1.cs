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

        private void Add_B_Click(object sender, EventArgs e)
        {
            Form2 Dialog = new Form2();
            Dialog.Text = "Добавление человека";
            if (Dialog.ShowDialog() == DialogResult.OK)
            {
                Person Chel = new Person();
                Chel.Surname = Dialog.Surname_TB.Text.Trim();
                Chel.BirthYear = Convert.ToInt32(Dialog.Year_TB.Text);
                Chel.Gender = Dialog.Male_RB.Checked ? "М" : "Ж";
                Chel.Height = Convert.ToInt32(Dialog.Height_TB.Text);
                listBox1.Items.Add(Chel);
            }
        }

        private void Change_B_Click(object sender, EventArgs e)
        {
            if (listBox1.SelectedIndex != -1)
            {
                Form2 Dialog = new Form2();
                Dialog.Text = "Изменение данных";
                Person Chel = listBox1.SelectedItem as Person;
                Dialog.Surname_TB.Text = Chel.Surname;
                Dialog.Year_TB.Text = Chel.BirthYear.ToString();
                if (Chel.Gender == "М")
                    Dialog.Male_RB.Checked = true;
                else
                    Dialog.Female_RB.Checked = true;
                Dialog.Height_TB.Text = Chel.Height.ToString();
                if (Dialog.ShowDialog() == DialogResult.OK)
                {
                    Chel.Surname = Dialog.Surname_TB.Text.Trim();
                    Chel.BirthYear = Convert.ToInt32(Dialog.Year_TB.Text);
                    Chel.Gender = Dialog.Male_RB.Checked ? "М" : "Ж";
                    Chel.Height = Convert.ToInt32(Dialog.Height_TB.Text);
                    listBox1.Items[listBox1.SelectedIndex] = Chel;
                }
            }
            else
                MessageBox.Show("Не выбран элемент в списке", "Информация");
        }

        private void Del_B_Click(object sender, EventArgs e)
        {
            if (listBox1.SelectedIndex != -1)
                listBox1.Items.RemoveAt(listBox1.SelectedIndex);
            else
                MessageBox.Show("Не выбран элемент в списке", "Информация");
        }
    }
}
