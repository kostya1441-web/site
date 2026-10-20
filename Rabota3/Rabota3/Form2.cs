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
    public partial class Form2 : Form
    {
        public Form2()
        {
            InitializeComponent();
        }

        private void Form2_FormClosing(object sender, FormClosingEventArgs e)
        {
            if (DialogResult == DialogResult.OK)
                try
                {
                    if (Surname_TB.Text.Trim() == "")
                    {
                        Surname_TB.Focus();
                        throw new Exception("Вы не ввели фамилию");
                    }
                    if (Year_TB.Text.Trim() == "")
                    {
                        Year_TB.Focus();
                        throw new Exception("Вы не ввели год рождения");
                    }
                    int Year;
                    try
                    {
                        Year = Convert.ToInt32(Year_TB.Text);
                    }
                    catch (FormatException)
                    {
                        Year_TB.Focus();
                        throw new Exception("Недопустимые символы в строке года рождения");
                    }
                    if (Year < 1900 || Year > DateTime.Now.Year)
                    {
                        Year_TB.Focus();
                        throw new Exception("Год рождения должен быть в диапазоне от 1900 до " + DateTime.Now.Year);
                    }
                    if (!Male_RB.Checked && !Female_RB.Checked)
                    {
                        Male_RB.Focus();
                        throw new Exception("Вы не выбрали пол");
                    }
                    if (Height_TB.Text.Trim() == "")
                    {
                        Height_TB.Focus();
                        throw new Exception("Вы не ввели рост");
                    }
                    int Height;
                    try
                    {
                        Height = Convert.ToInt32(Height_TB.Text);
                    }
                    catch (FormatException)
                    {
                        Height_TB.Focus();
                        throw new Exception("Недопустимые символы в строке роста");
                    }
                    if (Height < 30 || Height > 250)
                    {
                        Height_TB.Focus();
                        throw new Exception("Рост должен быть в диапазоне от 30 до 250");
                    }
                }
                catch (Exception Vse)
                {
                    e.Cancel = true;
                    MessageBox.Show(Vse.Message, "Ошибка");
                }
        }
    }
}
